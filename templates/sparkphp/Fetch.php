<?php

namespace SparkPHP;

// Simple HTTP request class using cURL
class Fetch {
    // Static method to make HTTP requests
    public static function request($url, $options = []) {
        $ch = curl_init($url);

        // Set HTTP method (GET, POST, etc.)
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method'] ?? 'GET');

        // Set request headers if provided
        if (!empty($options['headers'])) {
            $headers = [];
            foreach ($options['headers'] as $key => $value) {
                $headers[] = "$key: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Set request body if provided
        if (!empty($options['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string
        $response = curl_exec($ch); // Execute request
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP status code
        curl_close($ch); // Close cURL handle

        // Return an anonymous class instance for response handling
        return new class($response, $status) {
            public function __construct($response, $status) {
                $this->response = $response;
                $this->status = $status;
            }
            // Parse response as JSON
            public function json($assoc = true) {
                return json_decode($this->response, $assoc);
            }
            // Get response as plain text
            public function text() {
                return $this->response;
            }
            // Get HTTP status code
            public function status() {
                return $this->status;
            }
        };
    }
}
