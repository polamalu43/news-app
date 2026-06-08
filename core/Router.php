<?php

namespace Core;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private string $prefix = '';
    private array $middlewares = [];
    private static ?Router $instance = null;

    private function __construct() {}

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function get(string $path, mixed $handler, ?string $name = null): self
    {
        return $this->addRoute('GET', $path, $handler, $name);
    }

    public function post(string $path, mixed $handler, ?string $name = null): self
    {
        return $this->addRoute('POST', $path, $handler, $name);
    }

    public function put(string $path, mixed $handler, ?string $name = null): self
    {
        return $this->addRoute('PUT', $path, $handler, $name);
    }

    public function patch(string $path, mixed $handler, ?string $name = null): self
    {
        return $this->addRoute('PATCH', $path, $handler, $name);
    }

    public function delete(string $path, mixed $handler, ?string $name = null): self
    {
        return $this->addRoute('DELETE', $path, $handler, $name);
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');

        $method = strtoupper($method);

        // フォームから PUT/PATCH/DELETE を送るための処理
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['pattern'], $uri);

            if ($params === null) {
                continue;
            }

            // ミドルウェアを実行
            foreach ($route['middlewares'] as $middleware) {
                (new $middleware())->handle();
            }

            return $this->callHandler($route['handler'], $params);
        }

        $this->handleNotFound($uri);
        return null;
    }

    /** 名前付きルートから URL を生成 */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \InvalidArgumentException("Route [{$name}] not found.");
        }

        $path = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $path = preg_replace("/\{$key\??\}/", $value, $path);
        }

        // 未置換の任意パラメータを除去
        $path = preg_replace('/\/\{[^}]+\?\}/', '', $path);

        return $path;
    }

    private function addRoute(
        string $method,
        string $path,
        mixed  $handler,
        ?string $name
    ): self {
        $fullPath = '/' . trim($this->prefix . '/' . trim($path, '/'), '/');
        $fullPath = $fullPath === '' ? '/' : $fullPath;

        $pattern = $this->buildPattern($fullPath);

        $route = [
            'method'      => strtoupper($method),
            'path'        => $fullPath,
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $this->middlewares,
            'name'        => $name,
        ];

        $this->routes[] = $route;

        if ($name !== null) {
            $this->namedRoutes[$name] = $fullPath;
        }

        return $this;
    }

    /** パスを正規表現パターンへ変換 */
    private function buildPattern(string $path): string
    {
        // {id}      → 必須パラメータ
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        // {id?}     → 任意パラメータ
        $pattern = preg_replace('/\{([a-zA-Z_]+)\?\}/', '(?P<$1>[^/]*)?', $pattern);
        return '#^' . $pattern . '$#';
    }

    /** URI とパターンを照合し、名前付きキャプチャを返す */
    private function matchRoute(string $pattern, string $uri): ?array
    {
        if (!preg_match($pattern, $uri, $matches)) {
            return null;
        }

        // 文字列キーのみ抽出（数値インデックスを除く）
        return array_filter(
            $matches,
            fn($key) => is_string($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    /** ハンドラを呼び出す（クロージャ or [Controller, method]）*/
    private function callHandler(mixed $handler, array $params): mixed
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $instance = \Core\Container::make($class);

            // メソッドの引数をリフレクションで解決
            $reflection   = new \ReflectionMethod($instance, $method);
            $methodParams = array_map(function (\ReflectionParameter $param) use ($params) {
                $type = $param->getType();

                // ルートパラメータ（{id}など）はプリミティブ型なので$paramsから取得
                if ($type === null || $type->isBuiltin()) {
                    return $params[$param->getName()] ?? null;
                }

                // クラスの型宣言はContainerで解決
                return \Core\Container::make($type->getName());
            }, $reflection->getParameters());

            return $instance->$method(...array_values($methodParams));
        }

        throw new \InvalidArgumentException('Invalid route handler.');
    }

    private function handleNotFound(string $uri): void
    {
        http_response_code(404);
        echo "404 Not Found: {$uri}";
    }

    public function run(): void
    {
        require_once BASE_PATH . '/routes/web.php';
        require_once BASE_PATH . '/routes/api.php';

        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $uri = $_SERVER['REQUEST_URI'];
        if ($scriptDir !== '' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $uri = '/' . ltrim($uri, '/');

        $response = $router->dispatch($_SERVER['REQUEST_METHOD'], $uri);
        if ($response instanceof Response) {
            $response->send();
        }
    }
}
