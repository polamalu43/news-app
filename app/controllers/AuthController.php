<?php

namespace App\Controllers;
use App\Services\AuthService;
use App\Requests\AuthRequest;
use App\Models\User;
use Core\Controller;
use Core\Response;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $service
    ) {}

    public function login(AuthRequest $request): Response
    {
        config('newsapi.api.news_api_url');
        if (!$request->validate()) {
            return Response::json(['status' => 'error', 'errors' => $request->errors()], 422);
        }

        try {
            $userInfo = $this->service->authenticate(
                $request->input('email'),
                $request->input('password')
            );
            if (is_null($userInfo)) {
                return Response::json(['status' => 'error', 'message' => lang('invalid_credentials')], 401);
            }
            session_regenerate_id(true);
            $this->storeUserSession($userInfo);

            // 成功時のJSONレスポンス
            return Response::json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            // エラー時のJSONレスポンス
            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function me(): Response
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return Response::json([
                'authenticated' => false,
                'userInfo' => null,
            ]);
        }

        return Response::json([
            'authenticated' => true,
            'userInfo' => [
                'id'    => $_SESSION['user_id'],
                'nickname'  => $_SESSION['nickname'],
                'email' => $_SESSION['email']
            ],
        ]);
    }

    private function storeUserSession(User $userInfo): void
    {
        $_SESSION['user_id']     = $userInfo->id;
        $_SESSION['nickname']    = $userInfo->nickname;
        $_SESSION['email']       = $userInfo->email;
    }
}
