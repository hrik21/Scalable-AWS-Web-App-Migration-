<?php

namespace App\Core;

/**
 * Simple router for handling HTTP requests
 */
class Router
{
    private array $routes = [];
    
    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function put(string $path, string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function delete(string $path, string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch(string $method, string $uri): array
    {
        // Remove query string from URI
        $uri = parse_url($uri, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                return $this->callHandler($route['handler'], $uri, $route['path']);
            }
        }
        
        return [
            'status' => 404,
            'data' => ['error' => 'Route not found']
        ];
    }
    
    private function matchPath(string $routePath, string $uri): bool
    {
        // Simple pattern matching - convert {id} to regex
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }
    
    private function callHandler(string $handler, string $uri, string $routePath): array
    {
        [$controllerName, $method] = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            return [
                'status' => 500,
                'data' => ['error' => 'Controller not found']
            ];
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            return [
                'status' => 500,
                'data' => ['error' => 'Method not found']
            ];
        }
        
        // Extract parameters from URI
        $params = $this->extractParams($routePath, $uri);
        
        try {
            $result = $controller->$method(...$params);
            return [
                'status' => 200,
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'data' => ['error' => $e->getMessage()]
            ];
        }
    }
    
    private function extractParams(string $routePath, string $uri): array
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));
        
        $params = [];
        
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/\{([^}]+)\}/', $routeParts[$i])) {
                $params[] = $uriParts[$i] ?? null;
            }
        }
        
        return $params;
    }
}