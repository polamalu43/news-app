<?php

namespace App\Controllers;
use App\Services\FavoriteService;
use App\Requests\FavoriteRequest;
use Core\Controller;
use Core\Response;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteService $service
    ) {}

    public function add(FavoriteRequest $request): Response
    {
        if (!$request->validate()) {
            return Response::json(['status' => 'error', 'errors' => $request->errors()], 422);
        }

        try {
            $this->service->addFavorite(
                $request->input('user_id'),
                $request->input('news_id')
            );

            return Response::json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function remove(FavoriteRequest $request): Response
    {
        if (!$request->validate()) {
            return Response::json(['status' => 'error', 'errors' => $request->errors()], 422);
        }

        try {
            $this->service->removeFavorite(
                $request->input('user_id'),
                $request->input('news_id')
            );

            return Response::json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
