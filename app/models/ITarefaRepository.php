<?php

namespace app\models;

interface ITarefaRepository
{

    public function get_all() : array;

    public function exists(int $id) : bool;

    public function find_by_id(int $id) : Tarefa | null;

    public function create(Tarefa $tarefa) : Tarefa;

    public function update(Tarefa $tarefa) : Tarefa | null;

    public function delete(int $id) : void;

}
