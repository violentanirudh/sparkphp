<?php
namespace SparkPHP;

// HTTP Response handling class
class Response {
    protected $view;           // View renderer instance
    protected $session_flash;  // Flash message handler

    // Constructor: initialize view and flash handler
    public function __construct($viewPath = null) {
        $this->view = new Views(($viewPath ?: dirname(__DIR__) . '/' . 'views/'));
        $this->session_flash = new Flash();
    }

    // Set HTTP status code
    public function status($code) {
        http_response_code($code);
        return $this;
    }

    // Redirect to a different URL
    public function redirect($location) {
        header('Location: ' . $location);
        exit;
    }

    // Set a response header
    public function header($key, $value) {
        header("$key: $value");
        return $this;
    }

    // Send a plain response body
    public function send($body) {
        echo $body;
        exit;
    }

    // Send a JSON response
    public function json($data) {
        $this->header('Content-Type', 'application/json');
        echo json_encode($data);
        exit;
    }

    // Get the flash message handler
    public function flash() {
        return $this->session_flash;
    }

    // Render a view template
    public function render($filename, $data = []) {
        $this->view->render($filename, $data);
        exit;
    }
}
