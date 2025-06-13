<?php

namespace SparkPHP;

// Main application class
class App
{
    // Properties for core components and services
    protected $router;
    protected $config = [];
    protected $services = [];
    protected $session;
    protected $cookies;
    protected $flash;
    protected $auth;
    protected $jwt_secret;
    protected $error_handler;
    protected $config_path;

    // Constructor: starts session and initializes router and error handler
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->router = new Router();
        $this->error_handler = new ErrorHandler(true);
    }

    // Set custom error handler
    public function set_errors_handler($display)
    {
        $this->errorHandler = new ErrorHandler($display);
    }

    // Set views folder for the router
    public function views($folder) 
    {
        $this->router->views($folder, $this -> config);
    }

    // Add middleware to the router
    public function use($middleware)
    {
        $this->router->use($middleware);
    }

    // Add a route to the router
    public function add($method, $path, $handler, $middlewares = [])
    {
        $this->router->add($method, $path, $handler, $middlewares);
    }

    // Group routes under a prefix
    public function group($prefix, $callback)
    {
        $this->router->group($prefix, $callback);
    }

    // Run the router (start handling requests)
    public function run()
    {
        $this->router->run();
    }

    // Set a configuration value
    public function set_config($key, $value)
    {
        $this->config[$key] = $value;
    }

    // Get a configuration value
    public function get_config($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    // Get or create the database connection
    public function database($host = null, $user = null, $pass = null, $db = null) {
        if (!isset($this->services['database'])) {
            if (!$host || !$user || !$db) {
                throw new \Exception('Database configuration required');
            }
            $this->services['database'] = new Database($host, $user, $pass, $db);
        }
        return $this->services['database'];
    }

    // Get or create the session handler
    public function session() {
        if (!$this->session) $this->session = new Session();
        return $this->session;
    }

    // Get or create the cookies handler
    public function cookies() {
        if (!$this->cookies) $this->cookies = new Cookies();
        return $this->cookies;
    }

    // Get or create the flash message handler
    public function flash() {
        if (!$this->flash) $this->flash = new Flash();
        return $this->flash;
    }

    // Set the JWT secret key
    public function set_jwt_secret($value) {
        $this->jwt_secret = $value;
    }

    // Get or create the authentication handler
    public function auth()
    {
        if (!$this->auth) {
            if (!$this->jwt_secret) throw new \Exception('JWT secret required');
            $db = $this->database();
            $this->auth = new Auth($db, $this->jwt_secret);
        }
        return $this->auth;
    }

    public function load_config($path)
    {

        $this -> config_path = $path;

        if (file_exists($path)) {
            // Read and decode the JSON config file
            $config = json_decode(file_get_contents($path), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON in config file');
            }

            // Set each config item
            if (is_array($config)) {
                foreach ($config as $key => $value) {
                    $this->set_config($key, $value);
                }
            }
        } else {
            throw new \Exception('Config file not found');
        }
    }

    public function change_config($newConfig)
    {
        if (!file_exists($this -> config_path)) {
            return false;
        }

        $config = json_decode(file_get_contents($this -> config_path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        foreach ($newConfig as $key => $value) {
            if (array_key_exists($key, $config)) {
                $config[$key] = $value;
            }
        }

        $content = json_encode($config, JSON_PRETTY_PRINT);

        if ($content === false) {
            return false;
        }

        if (file_put_contents($this -> config_path, $content) === false) {
            return false;
        }

        return true;
    }


}
