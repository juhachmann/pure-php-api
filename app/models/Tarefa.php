<?php

namespace app\models;

use app\exceptions\ValidationException;
use DateTime;
use Exception;
use JsonSerializable;

class Tarefa implements JsonSerializable {

    private static ITarefaRepository $repository;

    private ?int $id;
    private string $title;
    private string $description;
    private string $status;
    private DateTime $dateStart;
    private DateTime $dateEnd;

    /**
     * @param int|null $id
     * @param string $title
     * @param string $description
     * @param string|null $status
     * @param string $dateStart
     * @param string $dateEnd
     * @throws ValidationException
     */
    public function __construct(
        ?int $id,
        string $title,
        string $description,
        ?string $status,
        string $dateStart,
        string $dateEnd)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setStatus($status);
        $this->setDateStart($dateStart);
        $this->setDateEnd($dateEnd);
    }

    /**
     * Injeta a implementação do repositório a ser utilizado para persistência
     */
    public static function setRepository(ITarefaRepository $repository): void
    {
        Tarefa::$repository = $repository;
    }

    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int|null $id Inteiro positivo ou null
     * @throws ValidationException
     */
    public function setId(?int $id): void
    {
        if($id == null) {
            $this->id = $id;
            return;
        }
        if($id <= 0) {
            throw new ValidationException("Id deve ser um inteiro positivo");
        }
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title Não pode ser uma string vazia
     * @throws ValidationException
     */
    public function setTitle(string $title): void
    {
        if(strlen(trim($title)) == 0) {
            throw new ValidationException("Título não pode estar vazio");
        }
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * @param string $description Não pode ser uma string vazia
     * @throws ValidationException
     */
    public function setDescription(string $description): void
    {
        if(strlen(trim($description)) == 0) {
            throw new ValidationException("Descrição não pode estar vazia");
        }
        $this->description = $description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string|null $status Valores aceitos para status: 'done' | 'ongoing'
     * @throws ValidationException
     */
    public function setStatus(?string $status): void
    {
        if($status == null) {
            $this->status = "ongoing";
            return;
        }
        $lowercase = strtolower($status);
        if($lowercase != "ongoing" && $lowercase != "done") {
            throw new ValidationException("Status só pode aceitar os valores 'done' e 'ongoing'");
        }
        $this->status = $lowercase;
    }

    /**
     * @return string Representação de Datetime em formato ISO
     */
    public function getDateStart(): string
    {
        return $this->dateStart->format('Y-m-d');
    }

    /**
     * @param string $dateStr Representação de Datetime em formato ISO
     * @throws ValidationException
     */
    public function setDateStart(string $dateStr): void
    {
        if(strlen($dateStr) == 0){
            throw new ValidationException("Data de início não pode ser nula");
        }
        try {
            $date = new DateTime($dateStr);
        } catch (Exception $e) {
            throw new ValidationException("Formato de data de início está inválido. Tente YYYY-mm-ddTHH:mm");
        }
        $this->dateStart = $date;
    }

    /**
     * @return string Representação de Datetime em formato ISO
     */
    public function getDateEnd(): string
    {
        return $this->dateEnd->format('Y-m-d');
    }


    /**
     * Data Final não pode ser igual ou anterior à data de início
     * @param string|null $dateStr Representação de Datetime em formato ISO
     * @throws ValidationException
     */
    public function setDateEnd(?string $dateStr): void
    {
        if(strlen($dateStr) == 0){
            throw new ValidationException("Prazo final não pode ser nulo");
        }
        try {
            $date = new DateTime($dateStr);
        } catch (Exception $e) {
            throw new ValidationException("Formato de data final está inválido. Tente YYYY-mm-ddTHH:mm");
        }
        if($date < $this->dateStart) {
            throw new ValidationException("O prazo final não pode ser anterior à data de início");
        }
        $this->dateEnd = $date;
    }

    /**
     * Cria uma nova tarefa ou atualiza uma tarefa existente
     * Uma tarefa é considerada existente se possui ID
     */
    public function save() : Tarefa {
        if ($this->id == null) {
            return Tarefa::$repository->create($this);
        } else {
            return Tarefa::$repository->update($this);
        }
    }

    /**
     * Retorna todos os registros de tarefas
     */
    public static function getAll() : array {
        return Tarefa::$repository->get_all();
    }

    /**
     * Retorna um registro dado o seu ID
     */
    public static function findById(int $id) : Tarefa | null {
        return Tarefa::$repository->find_by_id($id);
    }

    /**
     * Remove um registro dado o seu ID
     */
    public static function delete(int $id) : void {
        Tarefa::$repository->delete($id);
    }

    /**
     * Checa se um registro existe, dado o seu ID
     */
    public static function exists(int $id) : bool {
        return Tarefa::$repository->exists($id);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'status' => $this->getStatus(),
            'date_start' => $this->getDateStart(),
            'date_end' => $this->getDateEnd(),
        ];
    }

}
