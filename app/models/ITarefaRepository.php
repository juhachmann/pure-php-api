<?php

namespace app\models;

/**
 * Interface para utilização do padrão Repository
 */
interface ITarefaRepository
{

    /**
     * Recupera todas as tarefas do repositório
     */
    public function get_all() : array;

    /**
     * Verifica se uma tarefa com determinado ID existe no repositório
     */
    public function exists(int $id) : bool;

    /**
     * Recupera uma tarefa do repositório, dado o seu ID
     */
    public function find_by_id(int $id) : Tarefa | null;

    /**
     * Cria uma tarefa no repositório
     */
    public function create(Tarefa $tarefa) : Tarefa;

    /**
     * Atualiza uma tarefa no repositório, dado o seu ID
     */
    public function update(Tarefa $tarefa) : Tarefa | null;

    /**
     * Remove uma tarefa do repositório, dado o seu ID
     */
    public function delete(int $id) : void;

}
