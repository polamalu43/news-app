<?php

namespace Core;

abstract class Controller
{
    /**
     * viewを呼び出す
     */
    protected function view(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = BASE_PATH . '/resources/views/' . $name . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View [{$name}] not found.");
        }

        require $viewPath;
    }

    /**
     * JSONを返す
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * リダイレクト
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
