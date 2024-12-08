

class SQLException(Exception):
    """Exceções lançadas a partir de erros no banco de dados"""
    def __init__(self, query, exception):
        self.query = query
        self.exception = exception
        self.message = f"SQL Exception: '{query}' METHOD RAISED '{exception}'"


class NotFound(Exception):
    """Exceção para recurso não encontrado"""
    def __init__(self, param, value):
        self.message = f"Todo with '{param}' = '{value}' NOT FOUND'"


class InvalidData(Exception):
    """Exceção para erro de validação de dados"""
    def __init__(self, invalid_attributes: list[str]):
        self.message = invalid_attributes

