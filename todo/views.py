from datetime import datetime
from json import JSONEncoder


class TarefaSerializer(JSONEncoder):
    """JSON encoder para Tarefas
    Remove '_' de atributos privados
    Transforma datas em strings no formato ISO
    """
    def default(self, o):
        if isinstance(o, datetime):
            return o.isoformat()
        return {key.lstrip('_'): value for key, value in o.__dict__.items()}


class ResponseWrapper:
    """Wrapper para as respostas da API

    Padrão JSend:
    - Status: 'success', 'fail', 'error'
    - Data: envelopa o modelo de dados
    - Message: detalhes da resposta
    - Code: status HTTP
    """
    def __init__(self, status: str, data = None, code: int = None, message: str | list = None):
        self.status = status
        if data:
            self.data = data
        if message:
            self.message = message
        if code:
            self.code = code

