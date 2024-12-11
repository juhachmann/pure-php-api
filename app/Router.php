<?php

namespace app;

use app\views\ErrorResponse;
use app\views\FailResponse;
use Exception;
use app\exceptions\NotFoundException;
use app\exceptions\ValidationException;

class Router
{
    private array $routes = [];


    /**
     * Registra uma rota e o callback para resolvê-la.
     * Os argumentos para o callback são resolvidos dinamicamente a partir dos parâmetros da rota, inseridos sequencialmente, sem checagem de tipo
     * @param string $method
     * @param string $path
     * @param $controller
     * @param string $action
     * @return void
     */
    public function addRoute(string $method, string $path, $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }


    /**
     * Resolve uma rota a partir de uma requisição ao servidor.
     * Parâmetros são resolvidos dinamicamente, mas sem checagem de tipo.
     * @return void
     */
    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($method === 'OPTIONS') {
            $this->resolvePreflight();
            return;
        }

        foreach ($this->routes as $route) {
            $params = [];
            if ($method === $route['method'] &&
                ($uri === $route['path'] |
                $this->pathWithParams($route['path'], $uri, $params))) {
                try {
                    $response = call_user_func_array(
                        [$route['controller'], $route['action']],
                        $params);
                    $this->sendResponse($response, $response->code);
                } catch (Exception $e) {
                    $this->handleException($e);
                }
                return;
            }
        }

        $notFound = new FailResponse(
            404,
            null,
            "Endpoint não encontrado");
        $this->sendResponse($notFound, 404);

    }

    /**
     * Resolve request do tipo preflight para checar CORS
     * @return void
     */
    private function resolvePreflight() : void
    {
        $this->sendResponse(null, 204);
    }

    private function sendResponse(mixed $message, int $code): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        http_response_code($code);
        if ($message) {
            header('Content-Type: application/json');
            echo json_encode($message);
        }
    }

    /**
     * Simula um resolvedor de rotas dinâmico com REGEX para parâmetros
     *
     * @param string $path
     * @param string $uri
     * @param array $params
     * @return bool
     */
    private function pathWithParams(string $path, string $uri, array &$params): bool
    {
        $pattern = preg_replace('/\/:([\w]+)/', '/(?P<$1>[\w-]+)', $path);
        if (preg_match("#^$pattern$#", $uri, $matches)) {
            $paramsArray = array_filter(
                $matches,
                'is_string',
                ARRAY_FILTER_USE_KEY);
            foreach ($paramsArray as $value) {
                $params[] = $value;
            }
            return true;
        }
        return false;
    }

    private function handleException(Exception $ex): void
    {
        if($ex instanceof NotFoundException) {
            $errorResponse = new FailResponse(404, null, $ex->getMessage());
            $this->sendResponse($errorResponse, $errorResponse->code);
            return;
        }
        if($ex instanceof ValidationException) {
            $errorResponse = new FailResponse(400, null, $ex->getMessage());
            $this->sendResponse($errorResponse, $errorResponse->code);
            return;
        }
        $errorResponse = new ErrorResponse(
            500,
            null,
            "Ocorreu um erro em nossos servidores");
        $this->sendResponse($errorResponse, $errorResponse->code);
    }


}