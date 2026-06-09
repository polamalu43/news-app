<?php

namespace App\Controllers;
use App\Services\MypageService;
use Core\Controller;
use Core\Response;

class MypageController extends Controller
{
    public function __construct(
        private MypageService $service
    ) {}

    public function index(int $userId): Response
    {
        try {
            $user = $this->service->findUserById($userId)->toArray();
            $favoriteList = $this->service->getFavoriteList($userId);

            return Response::json([
                'status' => 'success',
                'data' => [
                    'user' => $user,
                    'favoriteList' => $favoriteList
                ]
            ], 200);
        } catch (\Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
