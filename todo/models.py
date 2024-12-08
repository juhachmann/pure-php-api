from datetime import datetime

from .exceptions import InvalidData
from .repositories import TarefaRepository


class TarefaModel:
    """Regras de Validação:
    - end_date: maior do que start_date
    - status: 'done' | 'ongoing'
    """

    repository: TarefaRepository

    required_fields = ["title", "description", "date_start"]

    @classmethod
    def set_repository(cls, repository: TarefaRepository):
        """Injeta a implementacao do repositório a ser usado para conexão com o banco de dados"""
        cls.repository = repository

    def __init__(self, title: str = None, description: str = None, date_start: datetime = None, date_end: datetime = None, id: int = None,
                 status: str = 'ongoing'):
        self.id = id
        self.title = title
        self.description = description
        self._status = status
        self.date_start = date_start
        self._date_end = date_end

    @property
    def status(self):
        return self._status

    @status.setter
    def status(self, status : str):
        status = status.lower()
        if status not in ["done", "ongoing"]:
            raise InvalidData(["Status só aceita valores 'done' e 'ongoing'"])
        self._status = status

    @property
    def date_end(self):
        return self._date_end

    @date_end.setter
    def date_end(self, date_end : datetime):
        if date_end <= self.date_start:
            raise InvalidData(["Prazo final não deve ser anterior à data de início da tarefa"])
        self._date_end = date_end

    def save(self) -> "TarefaModel":
        """Creates or updates a record"""
        if self.id is None:
            return self.repository.create(todo=self)
        self.repository.update(todo=self)
        return self

    @classmethod
    def get_all(cls) -> list["TarefaModel"]:
        """Retrieve all records from database"""
        db_result = cls.repository.get_all()
        if len(db_result) > 0:
            return [cls._map_to_object(row) for row in db_result]
        return []

    @classmethod
    def find_by_id(cls, todo_id: int) -> "Todo | None":
        """Finds one record by its ID"""
        result = cls.repository.find_by_id(todo_id=todo_id)
        if result is None:
            return result
        return cls._map_to_object(result)

    @classmethod
    def delete(cls, todo_id: int) -> None:
        """Deletes a record by its ID"""
        return cls.repository.delete(todo_id=todo_id)

    @classmethod
    def exists(cls, todo_id: int) -> bool:
        """Checks if record with given ID exists"""
        return cls.repository.exists(todo_id=todo_id)

    @classmethod
    def _map_to_object(cls, row: tuple) -> "TarefaModel":
        """Maps database tuple to TodoObject"""
        id = row[0]
        title = row[1]
        description = row[2]
        status = row[3]
        start = row[4]
        end = row[5]
        return TarefaModel(id=id, title=title, description=description, status=status, date_start=start, date_end=end)


    @classmethod
    def from_dict(cls, request_body: dict) -> "TarefaModel":
        """ Converte um dict em um objeto Tarefa
        Lança exceções de validação
        """
        todo = TarefaModel()
        validation_errors = ["Erros de validação:"]
        validation_errors += cls._check_required_fields(request_body)

        try:
            todo.id = request_body["id"]
        except:
            pass

        todo.title = request_body["title"]
        todo.description = request_body["description"]
        todo.date_start = request_body["date_start"]

        try:
            todo.date_start = datetime.fromisoformat(request_body["date_start"])
        except Exception as e:
            validation_errors.append("Data de início em formato inválido")

        if "date_end" in request_body and len(request_body["date_end"]) > 0:
            try:
                todo.date_end = datetime.fromisoformat(request_body["date_end"])
            except Exception as e:
                validation_errors.append("Data de início em formato inválido")

        if "status" in request_body:
            try:
                todo.status = request_body["status"]
            except InvalidData as e:
                validation_errors.append(e.message)

        if len(validation_errors) > 1:
            raise InvalidData(validation_errors)

        return todo

    @classmethod
    def _check_required_fields(cls, request: dict) -> list:
        missing = []
        for key in cls.required_fields:
            if key not in request:
                missing.append(key.capitalize() + " não pode ser nulo")
            elif isinstance(request[key], str) and len(request[key]) == 0:
                missing.append(key.capitalize() + " não pode ser vazio")
        return missing
