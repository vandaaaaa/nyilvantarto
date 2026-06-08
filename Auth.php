<?php

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::startSession();
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['email']      = $user['email'];
        $_SESSION['name']       = $user['name'] ?? $user['email'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['student_id'] = $user['student_id'] ?? null;
    }

    public static function logout(): void
    {
        self::startSession();
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header("Location: index.php?view=login");
            exit;
        }
    }

    public static function role(): ?string
    {
        self::startSession();
        return $_SESSION['role'] ?? null;
    }

  
    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isEditor(): bool
    {
        return in_array(self::role(), ['admin', 'editor']);
    }

    
    public static function canEdit(): bool
    {
        return self::isEditor();
    }


    public static function canManageUsers(): bool
    {
        return self::isAdmin();
    }

  

    public static function requireEdit(): void
    {
        self::requireLogin();
        if (!self::canEdit()) {
            self::forbidden();
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            self::forbidden();
        }
    }

    
    public static function requireTanar(): void
    {
        self::requireEdit();
    }

    public static function isTanar(): bool
    {
        return self::canEdit();
    }

    public static function isDiak(): bool
    {
   
        return self::role() === 'user';
    }

   

    public static function currentUserId(): ?int
    {
        self::startSession();
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function currentStudentId(): ?int
    {
        self::startSession();
        return isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : null;
    }

    public static function currentEmail(): ?string
    {
        self::startSession();
        return $_SESSION['email'] ?? null;
    }

    public static function currentName(): ?string
    {
        self::startSession();
        return $_SESSION['name'] ?? $_SESSION['email'] ?? null;
    }

    private static function forbidden(): void
    {
        http_response_code(403);
        echo "<h2>Hozzáférés megtagadva</h2><p>Nincs jogosultságod ehhez a művelethez.</p>";
        echo '<p><a href="index.php">Vissza a főoldalra</a></p>';
        exit;
    }
}
