<?php

namespace app\controllers;

use app\exceptions\NotFoundException;
use app\exceptions\ValidationException;
use app\models\Tarefa;
use app\utils\Sanitizer;
use app\views\ResponseWrapper;
use app\views\SuccessResponse;

class TarefaController
{

    private Sanitizer $sanitizer;

    /**
     * Constroi e injeta as dependências do Controller
     */
    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    /**
     * Cria uma nova tarefa na aplicação
     * @throws ValidationException
     */
    public function create() : ResponseWrapper{
        $request = $this->getRequest();
        $request = $this->sanitizer->escape_html($request);
        $tarefa = $this->buildTarefa($request);
        $tarefa = $tarefa->save();
        return new SuccessResponse(201, $tarefa);
    }

    /**
     * Recupera todas as tarefas da aplicação
     */
    public function getAll(): ResponseWrapper
    {
        $data = Tarefa::getAll();
        return new SuccessResponse(200, $data);
    }

    /**
     * Recupera uma tarefa pelo seu ID
     * @throws NotFoundException
     */
    public function getOne(int $id): ResponseWrapper
    {
        $data = Tarefa::findById($id);
        if($data == null) {
            throw new NotFoundException("Tarefa não encontrada");
        }
        return new SuccessResponse(200, $data);
    }

    /**
     * Atualiza os dados de uma tarefa
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update(int $id): ResponseWrapper
    {
        if(!Tarefa::exists($id)) {
            throw new NotFoundException("Tarefa não encontrada");
        }
        $request = $this->getRequest();
        $request = $this->sanitizer->escape_html($request);
        $tarefa = $this->buildTarefa($request);
        $tarefa->setId($id);
        $tarefa = $tarefa->save();
        return new SuccessResponse(200, $tarefa);
    }

    /**
     * Remove uma tarefa
     * @throws NotFoundException
     */
    public function delete(int $id): ResponseWrapper
    {
        if(!Tarefa::exists($id)) {
            throw new NotFoundException("Tarefa não encontrada");
        }
        Tarefa::delete($id);
        return new SuccessResponse(204);
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
        return new Tarefa($id, $title, $description, $status, $dateStart, $dateEnd);
    }

    private function getRequest() : mixed
    {
        return json_decode(file_get_contents('php://input'), true);
    }

}
