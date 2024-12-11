<?php

namespace app\controllers;

use app\exceptions\NotFoundException;
use app\exceptions\ValidationException;
use app\models\Tarefa;
use app\utils\Sanitizer;
use app\views\ResponseWrapper;

class TarefaController
{

    private Sanitizer $sanitizer;

    /**
     * @param Sanitizer $sanitizer
     */
    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }


    /**
     * @throws ValidationException
     */
    public function create() : ResponseWrapper{
        $request = $this->getRequest();
        $request = $this->sanitizer->sanitize($request);
        $tarefa = $this->buildTarefa($request);
        $tarefa = $tarefa->save();
        return new ResponseWrapper("success", 201, $tarefa);
    }

    /**
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function update(int $id): ResponseWrapper
    {
        if(!Tarefa::exists($id)) {
            throw new NotFoundException("Tarefa não encontrada");
        }
        $request = $this->getRequest();
        $request = $this->sanitizer->sanitize($request);
        $tarefa = $this->buildTarefa($request);
        $tarefa->setId($id);
        $tarefa = $tarefa->save();
        return new ResponseWrapper("success", 200, $tarefa);
    }

    /**
     * @throws NotFoundException
     */
    public function getOne(int $id): ResponseWrapper
    {
        $data = Tarefa::findById($id);
        if($data == null) {
            throw new NotFoundException("Tarefa não encontrada");
        }
        return new ResponseWrapper("success", 200, $data);
    }

    public function getAll(): ResponseWrapper
    {
        $data = Tarefa::getAll();
        return new ResponseWrapper("success", 200, $data);
    }

    /**
     * @throws NotFoundException
     */
    public function delete(int $id): ResponseWrapper
    {
        if(!Tarefa::exists($id)) {
            throw new NotFoundException("Tarefa não encontrada");
        }
        Tarefa::delete($id);
        return new ResponseWrapper("success", 204);
    }

    /**
     * @throws ValidationException
     */
    private function buildTarefa(array $input): Tarefa
    {
        $id = $input["id"] ?? null;
        $title = $input["title"];
        $description = $input["description"];
        $status = $input["status"] ?? null;
        $dateStart = $input["date_start"];
        $dateEnd = $input["date_end"] ?? null;
        return new Tarefa($id, $title, $description, $status,
            $dateStart, $dateEnd);
    }

    // O acesso ao "requestBody" não deve ficar aqui
    private function getRequest() : mixed
    {
        return json_decode(file_get_contents('php://input'), true);
    }

}
