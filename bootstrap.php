<?php
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/core/helpers.php';
loadEnv(BASE_PATH . '/.env');

ensureSessionStarted();

// オートローダー登録
spl_autoload_register(function (string $class): void {
    $file = BASE_PATH . '/' . strtolower(str_replace('\\', '/', $class)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
