<?php

namespace App\Middleware;

class RateLimiter {
    private const MAX_REQUESTS = 100;
    private const TIME_WINDOW = 3600; // 1 hour

    public static function check() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit:$ip";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 1, 'timestamp' => time()];
            return true;
        }

        $limit = $_SESSION[$key];
        if (time() - $limit['timestamp'] > self::TIME_WINDOW) {
            $_SESSION[$key] = ['count' => 1, 'timestamp' => time()];
            return true;
        }

        if ($limit['count'] >= self::MAX_REQUESTS) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Trop de requêtes. Veuillez réessayer plus tard.']);
            exit;
        }

        $_SESSION[$key]['count']++;
        return true;
    }

    public static function reset($ip = null) {
        $ip = $ip ?? $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit:$ip";
        unset($_SESSION[$key]);
    }
} 