from abc import ABC, abstractmethod
from typing import TYPE_CHECKING

if TYPE_CHECKING:
    from .models import TarefaModel


class TarefaRepository(ABC):
    """Interface para implementar o padrão repositório"""

    @abstractmethod
    def get_all(self) -> list[tuple]:
        """Retrieve all records"""
        pass

    @abstractmethod
    def exists(self, tarefa_id: int) -> bool :
        """Checks if entity exists"""
        pass

    @abstractmethod
    def find_by_id(self, tarefa_id: int) -> tuple | None:
        """Find one record by ID"""
        pass

    @abstractmethod
    def create(self, tarefa: "TarefaModel") -> "TarefaModel":
        """Create new record"""
        pass

    @abstractmethod
    def update(self, tarefa: "TarefaModel") -> None:
        """Update record"""
        pass

    @abstractmethod
    def delete(self, tarefa_id) -> None:
        """Removes record from database"""
        pass
