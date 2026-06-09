<?php

namespace App\Controllers;
use Core\Controller;
use Core\Response;
use App\Services\NewsService;

class NewsController extends Controller
{
    public function __construct(
        private NewsService $service
    ) {}

    public function index(): Response
    {
        try {
            return Response::json([
                'status' => 'success',
                'data' => $this->service->getNewsList()
            ], 200);
        } catch (\Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(): Response
    {
        try {
            $newsList = $this->service->fetchNews(1, 1, 5);
            $result = $this->service->insertNews($newsList);

            if (!$result) {
                return Response::json(['status' => 'error', 'message' => 'Failed to save news'], 500);
            }

            return Response::json(['status' => 'success'], 200);
        } catch (\InvalidArgumentException $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 400);
        } catch (\RuntimeException $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 502);
        } catch (\Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
