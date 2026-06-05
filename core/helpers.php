<?php
function loadEnv(string $path): void
{
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}

function dd(mixed ...$vars): never
{
    echo '<pre style="background:#000; color:#fff; padding:16px; border-radius:8px; font-size:14px;">';
    foreach ($vars as $var) {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace('/=>\s*\n\s*/', ' => ', $output);
        $output = preg_replace('/(\[(.+)\])/', '<span style="color:#8689c3;">$1</span>', $output);
        $output = preg_replace('/(\s=>\s)(.+)/', '$1<span style="color:#5aff19;">$2</span>', $output);
        echo $output;
    }
    echo '</pre>';
    die();
}

function ensureSessionStarted(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function lang(string $key): string
{
    static $messages = null;
    if ($messages === null) {
        $messages = require str_replace('\\', '/', BASE_PATH . '/resources/lang/ja/message.php');
    }

    $keys = explode('.', $key);
    $value = $messages;
    foreach ($keys as $segment) {
        $value = $value[$segment] ?? null;

        if ($value === null) {
            return $key;
        }
    }

    return $value;
}
