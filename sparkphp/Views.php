<?php

namespace SparkPHP;

// View rendering class
class Views
{
    private $basePath;    // Base path to view files
    private $shared = []; // Shared variables for all views

    // Constructor: set base path for views
    public function __construct($basePath, $shared = [])
    {
        $this->basePath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR;
        $this->shared = $shared;
    }

    // Share a variable with all views
    public function share($key, $value)
    {
        $this->shared[$key] = $value;
    }

    // Render a view file with provided data
    public function render($filename, $data = [])
    {
        $filename = str_replace('.php', '', $filename);
        $file = $this->basePath . $filename . '.php';
        if (!file_exists($file)) {
            http_response_code(500);
            echo "View not found: $filename";
            exit;
        }

        // Merge shared, local, and flash data
        $vars = array_merge($this->shared, $data);
        $flash = new Flash();
        $vars['flash'] = $flash;

        // Make $load available as a callable inside views AND partials
        $vars['load'] = function($partial, $data = []) {
            return $this->load($partial, array_merge($this->shared, $data));
        };

        extract($vars, EXTR_SKIP);
        include $file;
    }

    // Load a partial view file
    public function load($filename, $data = [])
    {
        $file = $this->basePath . $filename . '.php';
        if (!file_exists($file)) {
            echo "<!-- Partial not found: $filename -->";
            return;
        }

        // Merge shared + local data
        $vars = array_merge($this->shared, $data);

        // Include $load helper here too!
        $vars['load'] = function($partial, $data = []) {
            return $this->load($partial, array_merge($this->shared, $data));
        };

        extract($vars, EXTR_SKIP);
        include $file;
    }

}
