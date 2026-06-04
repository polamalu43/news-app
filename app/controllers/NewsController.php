<?php

namespace App\Controllers;
use Core\Controller;
use App\Services\NewsService;
use App\Requests\NewsRequest;
use App\Repositories\NewsRepository;

class NewsController extends Controller
{
    public function __construct(
        private NewsService $service
    ) {}

    public function index(): void
    {
        try {
            // 成功時のJSONレスポンス
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $this->service->getNewsList()
            ]);
        } catch (\Exception $e) {
            // エラー時のJSONレスポンス
            header('Content-Type: application/json', true, 500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function sync(): void
    {
        try {
            $this->service->fetchQiitaNews();

            // 成功時のJSONレスポンス
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);

        } catch (\Exception $e) {
            // エラー時のJSONレスポンス
            header('Content-Type: application/json', true, 500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
