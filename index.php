<?php
require_once __DIR__ . '/bootstrap.php';

try {
    \Core\Router::getInstance()->run();
} catch (\Throwable $e) {
    // ここで初めて500エラーを確定させる
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ]);
}
