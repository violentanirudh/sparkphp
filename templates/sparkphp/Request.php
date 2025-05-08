<?php

namespace SparkPHP;

// HTTP Request data wrapper class
class Request {
    public $uri;      // Request URI
    public $method;   // HTTP method (GET, POST, etc.)
    public $headers;  // HTTP headers
    public $cookies;  // Cookies
    public $params;   // Route parameters
    public $query;    // GET parameters
    public $json;     // JSON-decoded body (if applicable)
    public $form;     // POST parameters
    public $files;    // Uploaded files
    public $body;     // Raw request body

    // Constructor: initialize request data
    public function __construct($matches) {
        // Extract route parameters from $matches
        $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

        // Collect HTTP headers from $_SERVER
        $this->headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
                $this->headers[$headerName] = $value;
            }
        }
        // Add Content-Type and Content-Length headers if present
        foreach (['CONTENT_TYPE', 'CONTENT_LENGTH'] as $key) {
            if (isset($_SERVER[$key])) {
                $this->headers[strtolower(str_replace('_', '-', $key))] = $_SERVER[$key];
            }
        }

        // Set request properties from superglobals
        $this->uri     = $_SERVER['REQUEST_URI'];
        $this->method  = $_SERVER['REQUEST_METHOD'];
        $this->cookies = $_COOKIE;
        $this->query   = $_GET;
        $this->form    = $_POST;
        $this->files   = $_FILES;
        $this->body    = file_get_contents('php://input');

        // Parse JSON body if content-type is application/json
        $this->json = null;
        if (
            isset($this->headers['content-type']) &&
            stripos($this->headers['content-type'], 'application/json') !== false &&
            !empty($this->body)
        ) {
            $this->json = json_decode($this->body, true);
        }
    }
}
