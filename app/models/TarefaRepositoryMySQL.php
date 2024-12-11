<?php

namespace app\models;

use app\exceptions\ValidationException;
use app\models;
use Exception;
use PDO;
use PDOException;

/**
 * Implementação do repositório de Tarefas para banco de dados MySQL
 */
class TarefaRepositoryMySQL implements ITarefaRepository 
{
    private array $config;

    /**
     * @param array $dbConfig KEYS: 'host', 'username', 'password', 'database'
     */
    public function __construct(array $dbConfig)
    {
        $this->config = $dbConfig;
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function get_all(): array
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare("SELECT id, title, description, status, 
                   date_start, date_end FROM todos ORDER BY id DESC;");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tarefas = [];
            if ($result) {
                foreach ($result as $row) {
                    $tarefas[] = $this->mapToTarefa($row);
                }
            }
            $pdo = null;
            return $tarefas;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw new Exception($e->getMessage());
        }

    }

    /**
     * @throws Exception
     */
    public function exists(int $id): bool
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare("SELECT id FROM todos WHERE id = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $pdo = null;
            return count($result) > 0;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function find_by_id(int $id): Tarefa | null
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare("SELECT id, title, description, status, 
                   date_start, date_end FROM todos WHERE id = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $pdo = null;
            if(count($result) == 0)
                return null;
            return $this->mapToTarefa($result[0]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function create(models\Tarefa $tarefa): Tarefa
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare("INSERT INTO todos (title, description, status, 
                   date_start, date_end) VALUES (:title, :description, :status, 
                    :date_start, :date_end);");
            $title = $tarefa->getTitle();
            $stmt->bindParam(':title', $title);
            $description = $tarefa->getDescription();
            $stmt->bindParam(':description', $description);
            $status = $tarefa->getStatus();
            $stmt->bindParam(':status', $status);
            $dateStart = $tarefa->getDateStart();
            $stmt->bindParam(':date_start', $dateStart, PDO::PARAM_STR);
            $dateEnd = $tarefa->getDateEnd();
            $stmt->bindParam(':date_end', $dateEnd, PDO::PARAM_STR);
            $stmt->execute();
            $id = $pdo->lastInsertId();
            $pdo = null;
            $tarefa->setId($id);
            return $tarefa;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function update(Tarefa $tarefa): Tarefa | null
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare("UPDATE todos SET title = :title, 
                 description = :description, status = :status, date_start = :date_start, 
                 date_end = :date_end WHERE id = :id;");
            $title = $tarefa->getTitle();
            $stmt->bindParam(':title', $title);
            $description = $tarefa->getDescription();
            $stmt->bindParam(':description', $description);
            $status = $tarefa->getStatus();
            $stmt->bindParam(':status', $status);
            $dateStart = $tarefa->getDateStart();
            $stmt->bindParam(':date_start', $dateStart, PDO::PARAM_STR);
            $dateEnd = $tarefa->getDateEnd();
            $stmt->bindParam(':date_end', $dateEnd, PDO::PARAM_STR);
            $id = $tarefa->getId();
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $pdo = null;
            return $tarefa;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function delete(int $id): void
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();
            $pdo = null;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Inicializa as tabelas relacionadas a Tarefas na base de dados MySQL
     * @throws Exception
     */
    public function migrate() : void {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare("
                CREATE TABLE IF NOT EXISTS `todos` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `title` varchar(200) NOT NULL,
                    `description` varchar(500) NOT NULL,
                    `status` enum('done','ongoing') NOT NULL DEFAULT 'ongoing',
                    `date_start` datetime NOT NULL,
                    `date_end` datetime NOT NULL,
                    PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4
                COLLATE=utf8mb4_0900_ai_ci
            ");
            $stmt->execute();
            $pdo = null;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    private function connect(): PDO {
        $host = $this->config['host'];
        $username = $this->config['username'];
        $password = $this->config['password'];
        $database = $this->config['database'];

        $pdo =  new PDO(
            "mysql:host=$host;dbname=$database",
            $username,
            $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    /**
     * @throws ValidationException
     */
    private function mapToTarefa(mixed $result): Tarefa
    {
        $id = $result['id'];
        $title = $result['title'];
        $descr = $result['description'];
        $status = $result['status'];
        $date_start = $result['date_start'];
        $date_end = $result['date_end'];
        return new Tarefa($id, $title, $descr, $status, $date_start, $date_end);
    }

}
