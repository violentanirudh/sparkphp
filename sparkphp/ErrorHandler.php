<?php

namespace SparkPHP;

class ErrorHandler
{
    protected $displayErrors;
    protected bool $hasHandledError = false;

    public function __construct($displayErrors = true)
    {
        $this->displayErrors = $displayErrors;
        $this->register();
    }

    public function register()
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return;
        }

        $this->displayError('Error', $message, $file, $line, debug_backtrace());
    }

    public function handleException($exception)
    {
        $this->displayException($exception);
    }

    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error && ($error['type'] & error_reporting())) {
            $this->displayError('Shutdown Error', $error['message'], $error['file'], $error['line'], []);
        }
    }

    protected function displayError($type, $message, $file, $line, $trace = [])
    {
        if ($this->hasHandledError) {
            exit; // Prevent recursion
        }
        $this->hasHandledError = true;

        if ($this->displayErrors) {
            echo "<h1>$type</h1>";
            echo "<p><strong>Message:</strong> $message</p>";
            echo "<p><strong>File:</strong> $file</p>";
            echo "<p><strong>Line:</strong> $line</p>";

            if (!empty($trace)) {
                echo "<h2>Stack Trace:</h2>";
                echo "<pre>" . print_r($trace, true) . "</pre>";
            }

            exit;
        } else {
            $log = "$type: $message in $file on line $line";
            error_log($log);

            if (!empty($trace)) {
                error_log(print_r($trace, true));
            }

            echo "<h1>An error occurred. Please try again later.</h1>";
            exit;
        }
    }

    protected function displayException(\Throwable $exception)
    {
        if ($this->hasHandledError) {
            exit; // Prevent recursion
        }
        $this->hasHandledError = true;

        if ($this->displayErrors) {
            echo "<h1>Uncaught Exception</h1>";
            echo "<p><strong>Message:</strong> " . $exception->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $exception->getFile() . "</p>";
            echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
            echo "<h2>Stack Trace:</h2>";
            echo "<pre>" . $exception->getTraceAsString() . "</pre>";
            exit;
        } else {
            error_log("Uncaught Exception: " . $exception->getMessage() . " in " .
                $exception->getFile() . " on line " . $exception->getLine());
            error_log($exception->getTraceAsString());
            echo "<h1>An error occurred. Please try again later.</h1>";
            exit;
        }
    }
}
