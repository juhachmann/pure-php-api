<?php

namespace app;

use Exception;
use Dotenv\Dotenv;
use app\controllers\TarefaController;
use app\models\Tarefa;
use app\models\TarefaRepositoryMySQL;
use app\utils\Sanitizer;

class Bootstrap
{
    private Router $router;
    private TarefaController $tarefaController;
    private TarefaRepositoryMySQL $tarefaRepository;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function init(): void
    {
        $this->loadEnvironment();

        $this->setupDatabase();

        $this->instantiateClasses();

        $this->setupRoutes();

        $this->router->resolve();
    }

    private function loadEnvironment(): void
    {
        if (file_exists(__DIR__ . '/../.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        }
    }

    private function setupDatabase() : void
    {

        // Configurar a base de dados MySQL
        $config = [
            'host' => $_ENV['DB_HOST_MYSQL'],
            'username' => $_ENV['DB_USER_MYSQL'],
            'password' => $_ENV['DB_PASSWORD_MYSQL'],
            'database' => $_ENV['DB_DATABASE_MYSQL']
        ];

        $this->tarefaRepository = new TarefaRepositoryMySQL($config);

        try {
            $this->tarefaRepository->migrate();
        } catch (Exception $e) {
            echo($e->getMessage());
        }

    }

    private function instantiateClasses() : void {

        Tarefa::setRepository($this->tarefaRepository);

        $sanitizerUtil = new Sanitizer();
        $this->tarefaController = new TarefaController($sanitizerUtil);

    }

    private function setupRoutes() : void
    {

        $this->router->addRoute('GET', '/tarefas', $this->tarefaController, 'getAll');
        $this->router->addRoute('POST', '/tarefas', $this->tarefaController, 'create');
        $this->router->addRoute('GET', '/tarefas/:id', $this->tarefaController, 'getOne');
        $this->router->addRoute('PUT', '/tarefas/:id', $this->tarefaController, 'update');
        $this->router->addRoute('DELETE', '/tarefas/:id', $this->tarefaController, 'delete');

    }

}