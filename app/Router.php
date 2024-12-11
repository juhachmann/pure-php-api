<?php

namespace app;

use app\exceptions\NotFoundException;
use app\exceptions\ValidationException;
use app\views\ResponseWrapper;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class Router
{
    private array $routes = [];

    // Define a route for the specified method
    public function addRoute(string $method, string $path, $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    // Dispatch the request to the controller based on the URI and method
    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Lidando com request preflight para aceitar CORS
        if ($method === 'OPTIONS') {
            $this->sendResponse(null, 204);
            return;
        }

        $params = [];

        // Resolvendo as rotas
        foreach ($this->routes as $route) {
            if ($method === $route['method'] &&
                ($uri === $route['path'] |
                $this->matchPath($route['path'], $uri, $params))) {
                try {
                    $response = call_user_func_array([$route['controller'], $route['action']],
                        $params);
                    $this->sendResponse($response, $response->code);
                } catch (Exception $e) {
                    $this->handleException($e);
                }
                return;
            }
        }

        // 404 se rota não foi registrada:
        $this->sendResponse("Endpoint não encontrado", 404);
    }

    #[NoReturn] private function sendResponse(mixed $message, int $code): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        http_response_code($code);
        if ($message) {
            header('Content-Type: application/json');
            echo json_encode($message);
        }
        exit();
    }

    private function matchPath(string $routePath, string $uri, array &$params): bool
    {
        $pattern = preg_replace('/\/:([\w]+)/', '/(?P<$1>[\w-]+)', $routePath);
        if (preg_match("#^$pattern$#", $uri, $matches)) {
            $paramsArray = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            foreach ($paramsArray as $key => $value) {
                $params[] = $value;
            }
//            $params[] = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)[0];
            return true;
        }
        return false;
    }

    #[NoReturn] private function handleException(Exception $ex): void
    {
        if($ex instanceof NotFoundException) {
            $errorResponse = new ResponseWrapper(
                "fail",
                404,
                null,
                $ex->getMessage());
            $this->sendResponse($errorResponse, $errorResponse->code);
        }
        if($ex instanceof ValidationException) {
            $errorResponse = new ResponseWrapper(
                "fail",
                400,
                null,
                $ex->getMessage());
            $this->sendResponse($errorResponse, $errorResponse->code);
        }
        $errorResponse = new ResponseWrapper(
            "error",
            500,
            null,
            "Ocorreu um erro em nossos servidores");
        $this->sendResponse($errorResponse, $errorResponse->code);
    }


}