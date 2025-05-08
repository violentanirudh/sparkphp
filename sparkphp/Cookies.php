<?php

namespace SparkPHP;

// Cookie management class
class Cookies {
    // Set a cookie
    public function set($key, $value, $expire = 0, $path = '/', $secure = false, $httponly = true, $samesite = 'Lax') {
        // Use options array for PHP 7.3+ for better security and samesite support
        if (PHP_VERSION_ID >= 70300) {
            setcookie($key, $value, [
                'expires' => $expire ?: (time() + 3600 * 24 * 30),
                'path' => $path,
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => $samesite
            ]);
        } else {
            // Older PHP versions
            setcookie($key, $value, $expire ?: (time() + 3600 * 24 * 30), $path, '', $secure, $httponly);
        }
        $_COOKIE[$key] = $value; // Update the $_COOKIE superglobal
    }

    // Get a cookie value
    public function get($key, $default = null) { 
        return $_COOKIE[$key] ?? $default; 
    }

    // Delete a cookie
    public function delete($key, $path = '/') { 
        setcookie($key, '', time() - 3600, $path); // Set expiration in the past
        unset($_COOKIE[$key]); // Remove from $_COOKIE superglobal
    }
}
