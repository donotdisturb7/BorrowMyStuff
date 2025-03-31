<?php

namespace App\Middleware;

class AuthMiddleware {
    public static function authenticate() {
        if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole($role) {
        self::authenticate();
        if ($_SESSION['role'] !== $role) {
            header('Location: /403');
            exit;
        }
    }

    public static function isAuthenticated() {
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }

    public static function hasRole($role) {
        return self::isAuthenticated() && $_SESSION['role'] === $role;
    }
} 