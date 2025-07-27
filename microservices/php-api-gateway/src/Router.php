<?php

namespace App;

use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\HealthController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        // Health check
        $this->addRoute('GET', '/health', HealthController::class, 'check');
        
        // Category routes
        $this->addRoute('GET', '/api/categories', CategoryController::class, 'index');
        $this->addRoute('POST', '/api/categories', CategoryController::class, 'store');
        $this->addRoute('GET', '/api/categories/{id}', CategoryController::class, 'show');
        $this->addRoute('PUT', '/api/categories/{id}', CategoryController::class, 'update');
        $this->addRoute('DELETE', '/api/categories/{id}', CategoryController::class, 'destroy');

        // Product routes
        $this->addRoute('GET', '/api/products', ProductController::class, 'index');
        $this->addRoute('POST', '/api/products', ProductController::class, 'store');
        $this->addRoute('GET', '/api/products/{id}', ProductController::class, 'show');
        $this->addRoute('PUT', '/api/products/{id}', ProductController::class, 'update');
        $this->addRoute('DELETE', '/api/products/{id}', ProductController::class, 'destroy');
        $this->addRoute('GET', '/api/products/category/{categoryId}', ProductController::class, 'getByCategory');
        $this->addRoute('GET', '/api/products-with-category/{categoryId}', ProductController::class, 'getWithCategoryDetails');
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path, $params)) {
                // Add route parameters to request attributes
                foreach ($params as $key => $value) {
                    $request = $request->withAttribute($key, $value);
                }

                $controller = new $route['controller']();
                $action = $route['action'];

                if (!method_exists($controller, $action)) {
                    return $this->errorResponse('Method not found', 500);
                }

                return $controller->$action($request);
            }
        }

        return $this->errorResponse('Route not found', 404);
    }

    private function matchPath(string $routePath, string $requestPath, &$params = []): bool
    {
        $params = [];
        
        // Convert route path to regex pattern
        $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestPath, $matches)) {
            // Extract named parameters
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }

    private function errorResponse(string $message, int $status = 404): ResponseInterface
    {
        $response = new Response($status);
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}