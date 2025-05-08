<?php

namespace SparkPHP;

// Security helper class for CSRF, sanitization, and CORS
class Security
{
    // Generate or get CSRF token from session
    public static function get_csrf_token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Output CSRF hidden input field for forms
    public static function csrf_field()
    {
        $token = self::get_csrf_token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    // Validate CSRF token from POST or header
    public static function validate_csrf_token($token = null)
    {
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        $session_token = $_SESSION['csrf_token'] ?? '';
        return !empty($token) && !empty($session_token) && hash_equals($session_token, $token);
    }

    // Sanitize a string for safe output
    public static function sanitize_string($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    // Sanitize an email address
    public static function sanitize_email($email)
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    // Sanitize a URL
    public static function sanitize_url($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    // Sanitize an integer
    public static function sanitize_int($int)
    {
        return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }

    // Set CORS and security headers
    public static function cors($allowed_origins = ['*'])
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        if (in_array('*', $allowed_origins) || in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Authorization, X-CSRF-Token");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");
        }

        // Additional security headers
        header("X-Frame-Options: SAMEORIGIN");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: no-referrer-when-downgrade");
        header("X-XSS-Protection: 1; mode=block");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        header("Cross-Origin-Resource-Policy: same-origin"); // Similar to helmet.crossOriginResourcePolicy

        // End preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit();
        }
    }
}
