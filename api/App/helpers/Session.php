<?php

class Session
{
    /** Inactivity timeout: 30 minutes */
    private const TIMEOUT_SECONDS = 1800;

    

    public static function getUserId(): int|string|null
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function getUserRole(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

   
    public static function isAuthenticated(): bool
    {
        $id = self::getUserId();
        return $id !== null && (int)$id > 0;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

   
    public static function destroy(): void
    {
        // 1. Clear all session variables from memory
        $_SESSION = [];
        session_unset();

        // 2. Expire the browser cookie so it is removed immediately
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,   // past timestamp forces deletion
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // 3. Destroy the server-side session storage
        session_destroy();
    }


    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

   

   
    public static function checkTimeout(): void
    {
        // Only enforce timeout for authenticated sessions
        if (!self::isAuthenticated()) {
            return;
        }

        if (isset($_SESSION['_last_activity'])) {
            $idle = time() - (int)$_SESSION['_last_activity'];
            if ($idle > self::TIMEOUT_SECONDS) {
                self::destroy();
                http_response_code(401);
                echo json_encode(['error' => 'Session expired. Please log in again.']);
                exit();
            }
        }

       
        $_SESSION['_last_activity'] = time();
    }

    
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

  
    public static function validateCsrfToken(string $token): bool
    {
        $stored = $_SESSION['_csrf_token'] ?? '';
        if (empty($stored) || empty($token)) {
            return false;
        }
        return hash_equals($stored, $token);
    }
}
