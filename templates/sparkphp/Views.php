<?php

namespace SparkPHP;

// View rendering class
class Views
{
    private $basePath;    // Base path to view files
    private $shared = []; // Shared variables for all views

    // Constructor: set base path for views
    public function __construct($basePath)
    {
        $this->basePath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR;
    }

    // Share a variable with all views
    public function share($key, $value)
    {
        $this->shared[$key] = $value;
    }

    // Render a view file with provided data
    public function render($filename, $data = [])
    {
        $file = $this->basePath . $filename . '.php';
        if (!file_exists($file)) {
            http_response_code(500);
            echo "View not found: $filename";
            exit;
        }
        // Merge shared and local data
        $vars = array_merge($this->shared, $data);
        $flash = new Flash();
        $vars = array_merge($vars, [ 'flash' => $flash ]);

        // Helper to load partials inside views
        $load = function($partial, $data = []) {
            return $this->load($partial, $data);
        };

        extract($vars, EXTR_SKIP); // Extract variables for use in view
        include $file;             // Include the view file
    }

    // Load a partial view file
    public function load($filename, $data = [])
    {
        $file = $this->basePath . $filename . '.php';
        if (!file_exists($file)) {
            echo "<!-- Partial not found: $filename -->";
            return;
        }
        $vars = array_merge($this->shared, $data);
        extract($vars, EXTR_SKIP);
        include $file;
    }
}
