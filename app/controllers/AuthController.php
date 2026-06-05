<?php

namespace App\Controllers;
use Core\Controller;
use Core\Response;
use App\Services\AuthService;
use App\Requests\AuthRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $service
    ) {}

    public function login(AuthRequest $request): Response
    {
        if (!$request->validate()) {
            return Response::json(['status' => 'error', 'errors' => $request->errors()], 422);
        }

        try {
            $userInfo = $this->service->authenticate(
                $request->get('email'),
                $request->get('password')
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
                'name'  => $_SESSION['name'],
                'email' => $_SESSION['email']
            ],
        ]);
    }

    private function storeUserSession(User $userInfo): void
    {
        $_SESSION['user_id'] = $userInfo->id;
        $_SESSION['name']    = $userInfo->name;
        $_SESSION['email']   = $userInfo->email;
    }
}
