<?php

namespace SparkPHP;

// Session management class
class Session {
    
    // Set a session variable
    public function set($key, $value) { 
        $_SESSION[$key] = $value; 
    }

    // Get a session variable, or return default if not set
    public function get($key, $default = null) { 
        return $_SESSION[$key] ?? $default; 
    }

    // Delete a session variable
    public function delete($key) { 
        unset($_SESSION[$key]); 
    }
}
