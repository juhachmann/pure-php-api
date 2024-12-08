from html import escape

from todo.exceptions import NotFound, InvalidData
from .models import TarefaModel
from .views import ResponseWrapper

"""
Controller para ações relacionadas a Tarefas
(métodos estáticos foram extraídos para funções simples e a classe foi eliminada)
"""

def add(todo_dict: dict) -> ResponseWrapper:
    """Cria uma tarefa e retorna a resposta adequada"""
    todo_dict = sanitize_dict(todo_dict)
    todo = TarefaModel.from_dict(todo_dict)
    saved = todo.save()
    return ResponseWrapper(status="Success", data=saved)

def find_by_id(todo_id: int) -> ResponseWrapper:
    """Busca uma tarefa e retorna a resposta adequada"""
    valida_param_id(todo_id)
    todo = TarefaModel.find_by_id(todo_id)
    if todo is None:
        raise NotFound('id', todo_id)
    return ResponseWrapper(status="Success", data=todo)

def get_all() -> ResponseWrapper:
    """Recupera toda as tarefas e retorna a resposta adequada"""
    todos = TarefaModel.get_all()
    return ResponseWrapper(status="Success", data=todos)

def edit(todo_id: int, todo_dict: dict) -> ResponseWrapper:
    """Edita uma tarefa e retorna a resposta adequada"""
    valida_param_id(todo_id)
    if not TarefaModel.exists(todo_id):
        raise NotFound('id', todo_id)
    todo_dict = sanitize_dict(todo_dict)
    todo = TarefaModel.from_dict(todo_dict)
    todo.id = todo_id
    saved = todo.save()
    return ResponseWrapper(status="Success", data=saved)

def delete(todo_id: int) -> ResponseWrapper:
    """Deleta uma tarefa e retorna a resposta adequada"""
    valida_param_id(todo_id)
    if not TarefaModel.exists(todo_id=todo_id):
        raise NotFound('id', todo_id)
    TarefaModel.delete(todo_id)
    return ResponseWrapper(status="Success", data=None)

def valida_param_id(tarefa_id: int) -> None:
    """Validação simples do parâmetro ID"""
    if tarefa_id <= 0:
        raise InvalidData(["Id deve ser maior do que zero"])

def sanitize_dict(request_body: dict[any, str]) -> dict:
    """Utiliza html.escape para sanitizar os inputs
    Aceita um dicionário com valores string
    """
    for key, value in request_body.items():
        if isinstance(value, str):
            request_body[key] = escape(value)
    return request_body
