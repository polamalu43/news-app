<?php
namespace Core;

class Container
{
    private static array $bindings   = [];
    private static array $singletons = [];

    /** クロージャまたは実装クラスを登録 */
    public static function bind(string $abstract, \Closure|string $factory): void
    {
        self::$bindings[$abstract] = $factory;
    }

    /** シングルトンとして登録 */
    public static function singleton(string $abstract, \Closure|string $factory): void
    {
        self::bind($abstract, $factory);
        self::$singletons[$abstract] = null;
    }

    /** クラスを解決して返す */
    public static function make(string $abstract): object
    {
        // シングルトンが既に生成済みなら返す
        if (array_key_exists($abstract, self::$singletons)) {
            if (self::$singletons[$abstract] !== null) {
                return self::$singletons[$abstract];
            }
        }

        $factory  = self::$bindings[$abstract] ?? $abstract;
        $instance = $factory instanceof \Closure
            ? $factory()
            : self::resolve($factory);

        // シングルトンならキャッシュ
        if (array_key_exists($abstract, self::$singletons)) {
            self::$singletons[$abstract] = $instance;
        }

        return $instance;
    }

    /** リフレクションでコンストラクタの依存を自動解決 */
    private static function resolve(string $concrete): object
    {
        $reflection = new \ReflectionClass($concrete);

        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class [{$concrete}] is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $concrete();
        }

        $dependencies = array_map(function (\ReflectionParameter $param) use ($concrete) {
            $type = $param->getType();

            if ($type === null || $type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    return $param->getDefaultValue();
                }
                throw new \Exception(
                    "Cannot resolve parameter [{$param->getName()}] in [{$concrete}]."
                );
            }

            return self::make($type->getName());
        }, $constructor->getParameters());

        return $reflection->newInstanceArgs($dependencies);
    }
}
