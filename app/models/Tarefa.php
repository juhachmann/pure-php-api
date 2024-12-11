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
    private ?DateTime $dateEnd;

    /**
     * @param int|null $id
     * @param string $title
     * @param string $description
     * @param string|null $status
     * @param string $dateStart
     * @param string|null $dateEnd
     * @throws ValidationException
     */
    public function __construct(?int $id, string $title, string $description,
                    ?string $status, string $dateStart, ?string $dateEnd)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setStatus($status);
        $this->setDateStart($dateStart);
        $this->setDateEnd($dateEnd);
    }

    public static function setRepository(ITarefaRepository $repository): void
    {
        Tarefa::$repository = $repository;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
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
     * @throws ValidationException
     */
    public function setTitle(string $title): void
    {
        if(strlen($title) == 0) {
            throw new ValidationException("Título não pode estar vazio");
        }
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @throws ValidationException
     */
    public function setDescription(string $description): void
    {
        if(strlen($description) == 0) {
            throw new ValidationException("Descrição não pode estar vazia");
        }
        $this->description = $description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
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

    public function getDateStart(): string
    {
        return $this->dateStart->format('Y-m-d\TH:i');
    }

    /**
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

    public function getDateEnd(): string|null
    {
        if($this->dateEnd == null) return null;
        return $this->dateEnd->format('Y-m-d\TH:i');
    }

    /**
     * @throws ValidationException
     */
    public function setDateEnd(?string $dateStr): void
    {
        if ($dateStr == null || strlen($dateStr) == 0) {
            $this->dateEnd = null;
            return;
        }
        try {
            $date = new DateTime($dateStr);
        } catch (Exception $e) {
            throw new ValidationException("Formato de data final está inválido. Tente YYYY-mm-ddTHH:mm");
        }
        if($date <= $this->dateStart) {
            throw new ValidationException("O prazo final não pode ser anterior ou igual à data de início");
        }
        $this->dateEnd = $date;
    }

    public function save() : Tarefa {
        if ($this->id == null) {
            return Tarefa::$repository->create($this);
        } else {
            return Tarefa::$repository->update($this);
        }
    }

    public static function getAll() : array {
        return Tarefa::$repository->get_all();
    }

    public static function findById(int $id) : Tarefa | null {
        return Tarefa::$repository->find_by_id($id);
    }

    public static function delete(int $id) : void {
        Tarefa::$repository->delete($id);
    }

    public static function exists(int $id) : bool {
        return Tarefa::$repository->exists($id);
    }

    public function jsonSerialize()
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
