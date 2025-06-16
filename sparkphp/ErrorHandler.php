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
        if (!(error_reporting() & $severity) || $this->hasHandledError) {
            return;
        }

        $this->hasHandledError = true;
        $this->displayError('Error', $message, $file, $line, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
    }

    public function handleException($exception)
    {
        if ($this->hasHandledError) {
            return;
        }

        $this->hasHandledError = true;
        $this->displayException($exception);
    }

    public function handleShutdown()
    {
        $error = error_get_last();

        if ($error && ($error['type'] & error_reporting()) && !$this->hasHandledError) {
            $this->hasHandledError = true;
            $this->displayError('Shutdown Error', $error['message'], $error['file'], $error['line'], []);
        }
    }

    protected function displayError($type, $message, $file, $line, $trace = [])
    {
        $log = "$type: $message in $file on line $line";
        error_log($log);

        if (!empty($trace)) {
            error_log("Trace:\n" . print_r($trace, true));
        }

        if ($this->displayErrors) {
            $this->showErrorHTML($type, $message, $file, $line, $trace);
        } else {
            $this->redirectToErrorPage();
        }
    }

    protected function displayException(\Throwable $exception)
    {
        error_log("Uncaught Exception: " . $exception->getMessage() . " in " .
            $exception->getFile() . " on line " . $exception->getLine());
        error_log("Trace:\n" . $exception->getTraceAsString());

        if ($this->displayErrors) {
            $this->showErrorHTML('Uncaught Exception', $exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTrace());
        } else {
            $this->redirectToErrorPage();
        }
    }

    protected function showErrorHTML($type, $message, $file, $line, $trace)
    {
        echo "<div style='font-family: sans-serif; padding: 2rem; max-width: 700px; margin: auto;'>";
        echo "<h2 style='color: darkred;'>$type</h2>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($message) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($file) . "</p>";
        echo "<p><strong>Line:</strong> " . $line . "</p>";

        if (!empty($trace)) {
            echo "<p><strong>Trace (first 3 calls):</strong></p><ul>";
            foreach (array_slice($trace, 0, 3) as $item) {
                $func = $item['function'] ?? '';
                $class = $item['class'] ?? '';
                $traceFile = $item['file'] ?? '[internal]';
                $traceLine = $item['line'] ?? 'n/a';
                echo "<li>$class$func() in $traceFile on line $traceLine</li>";
            }
            echo "</ul>";
        }

        echo "<p style='margin-top: 2rem; font-size: 0.9em; color: gray;'>This is a development error preview.</p>";
        echo "</div>";
        exit;
    }

    protected function redirectToErrorPage()
    {
        if (!headers_sent()) {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            header("Location: http://$host/error");
        } else {
            echo "<script>window.location.href='/error';</script>";
        }
        exit;
    }
}
