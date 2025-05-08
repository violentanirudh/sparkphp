<?php

namespace SparkPHP;

// Simple routing and middleware handler
class Router {
    private $routes = [];         // Registered routes
    private $middlewares = [];    // Global middlewares
    private $current_path = '';   // Current group path prefix
    private $views_folder = '';   // Path to views folder

    // Register a global middleware
    public function use($middleware) {
        $this->middlewares[] = $middleware;
    }

    // Set the views folder path
    public function views($path) {
        $this->views_folder = $path;
    }

    // Group routes under a common prefix
    public function group($path, $callback) {
        $previous_path = $this->current_path;
        $this->current_path = rtrim($this->current_path, '/') . rtrim($path, '/');
        $callback($this);
        $this->current_path = $previous_path;
    }

    // Add a new route
    public function add($method, $path, $callback, $middlewares = []) {
        $method = strtoupper($method);
        $path = $this->current_path . '/' . ltrim($path, '/');
        $full_path = strtolower(rtrim($path, '/'));

        array_push($this->routes, [$method, $full_path, $callback, $middlewares]);
    }

    // Run the router (match request to route and execute handler)
    public function run() {
        $req_path = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $req_method = $_SERVER['REQUEST_METHOD'];
        $response = new Response($this->views_folder);


        foreach ($this->routes as $route_info) {
            [$method, $path, $callback, $route_middlewares] = $route_info;

            if ($req_method !== $method) continue;

            // Convert route path to regex for parameter matching
            $regex = preg_replace('/:([a-z]\w*)/', '(?P<$1>[^/]+)', $path);

            if (preg_match("#^$regex$#", $req_path, $matches)) {

                $request = new Request($matches);

                // Combine global and route-specific middlewares
                $middlewares = array_merge($this->middlewares, $route_middlewares);            

                // Build middleware pipeline
                $handler = array_reduce(
                    array_reverse($middlewares),
                    function ($next, $middleware) {
                        return function ($request, $response) use ($middleware, $next) {
                            return $middleware($request, $response, $next);
                        };
                    },
                    $callback
                );

                // Execute the handler pipeline
                return $handler($request, $response);
            }
        }

        // No matching route found
        http_response_code(404);
        return $response -> render('404.php');
    }
}
