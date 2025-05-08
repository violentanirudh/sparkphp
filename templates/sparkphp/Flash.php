<?php 

namespace SparkPHP;

// Flash message handler class
class Flash {
    // Set a flash message
    public function set($key, $value) { 
        $_SESSION['_flash'][$key] = $value; 
    }

    // Get a flash message and remove it from session
    public function get($key) {
        if (isset($_SESSION['_flash'][$key])) {
            $value = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $value;
        }
        return null;
    }

    // Get all flash messages and clear them from session
    public function all() {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }
}
