<?php
namespace Core;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // 認証成功時、セッションに保持
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }

    // ログイン中かどうかの判定
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    // 現在のユーザー情報を取得
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    // ログアウト
    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }
}
