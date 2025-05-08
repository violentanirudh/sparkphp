<?php

namespace SparkPHP;

// Error and exception handling class
class ErrorHandler
{
    protected $displayErrors; // Whether to display errors or not

    // Constructor: set display option and register handlers
    public function __construct($displayErrors = true)
    {
        $this->displayErrors = $displayErrors;
        $this->register();
    }

    // Register error, exception, and shutdown handlers
    public function register()
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    // Handle PHP errors
    public function handleError($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return;
        }
        $this->displayError('Error', $message, $file, $line);
    }

    // Handle uncaught exceptions
    public function handleException($exception)
    {
        $this->displayError('Exception', $exception->getMessage(), $exception->getFile(), $exception->getLine());
    }

    // Handle fatal errors on shutdown
    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error && ($error['type'] & error_reporting())) {
            $this->displayError('Shutdown Error', $error['message'], $error['file'], $error['line']);
        }
    }

    // Display or log the error based on the displayErrors flag
    protected function displayError($type, $message, $file, $line)
    {
        if ($this->displayErrors) {
            // Show error details to the user (for development)
            echo "<h1>$type</h1>";
            echo "<p><strong>Message:</strong> $message</p>";
            echo "<p><strong>File:</strong> $file</p>";
            echo "<p><strong>Line:</strong> $line</p>";
            exit;
        } else {
            // Log error and show generic message (for production)
            error_log("$type: $message in $file on line $line");
            echo "<h1>An error occurred. Please try again later.</h1>";
            exit;
        }
    }
}
