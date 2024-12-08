from mysql.connector import connect, Error
from typing import TYPE_CHECKING

from todo.repositories import TarefaRepository
from todo.exceptions import SQLException

if TYPE_CHECKING:
    from .models import TarefaModel

class TarefaRepositoryMySQL(TarefaRepository):
    """Implementação de TarefaRepository para banco de dados MySQL"""

    def __init__(self, connection_config):
        """
        Inicializa a classe com as configurações necessárias para conexão com base de dados MySQL:
        host, user, password, database
        """
        self.connection_config = connection_config

    def get_all(self):
        try:
            conn = self._connect()
            cursor = conn.cursor(prepared=True)
            stmt = "SELECT id, title, description, status, date_start, date_end FROM todos;"
            cursor.execute(stmt)
            result = cursor.fetchall()
            conn.close()
            return result
        except Error as e:
            print(e)
            raise SQLException('get_all', e.msg)

    def find_by_id(self, todo_id: int):
        try:
            conn = self._connect()
            cursor = conn.cursor(prepared=True)
            stmt = ("SELECT id, title, description, status, date_start, date_end FROM todos "
                    "WHERE id = %s;")
            cursor.execute(stmt, (todo_id,))
            result = cursor.fetchone()
            conn.close()
            print(result)
            return result
        except Error as e:
            print(e)
            raise SQLException('find_by_id', e.msg)

    def create(self, todo: "TarefaModel"):
        try:
            conn = self._connect()
            cursor = conn.cursor(prepared=True)
            stmt = ("INSERT INTO todos(title, description, status, date_start, date_end) "
                    " VALUES (%s, %s, %s, %s, %s);")
            cursor.execute(stmt, (todo.title, todo.description, todo.status,
                                  todo.date_start, todo.date_end))
            todo.id = cursor.lastrowid
            conn.commit()
            conn.close()
            return todo
        except Error as e:
            print(e)
            raise SQLException('create', e.msg)

    def update(self, todo: "TarefaModel"):
        try:
            conn = self._connect()
            cursor = conn.cursor(prepared=True)
            stmt = ("UPDATE todos SET title = %s, description = %s, status = %s, "
                    "date_start = %s, date_end = %s "
                    "WHERE id = %s;")
            cursor.execute(stmt, (todo.title, todo.description, todo.status,
                                  todo.date_start, todo.date_end, todo.id))
            conn.commit()
            conn.close()
        except Error as e:
            print(e)
            raise SQLException('update', e.msg)

    def exists(self, todo_id: int):
        try:
            conn = self._connect()
            cursor = conn.cursor(prepared=True)
            stmt = "SELECT id FROM todos WHERE id = %s;"
            cursor.execute(stmt, (todo_id,))
            cursor.fetchone()
            row_count = cursor.rowcount
            conn.close()
            return row_count > 0
        except Error as e:
            print(e)
            raise SQLException('update', e.msg)

    def delete(self, todo_id: int):
        """Removes record from database"""
        try:
            conn = self._connect()
            cursor = conn.cursor(prepared=True)
            stmt = "DELETE FROM todos WHERE id = %s;"
            cursor.execute(stmt, (todo_id,))
            conn.commit()
            conn.close()
        except Error as e:
            print(e)
            raise SQLException('delete', e.msg)

    def migrate(self):
        try:
            conn = self._connect()
            cursor = conn.cursor(prepared=True)
            stmt = """CREATE TABLE IF NOT EXISTS `todos` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `title` varchar(200) NOT NULL,
                    `description` varchar(500) NOT NULL,
                    `status` enum('done','ongoing') NOT NULL DEFAULT 'ongoing',
                    `date_start` datetime NOT NULL,
                    `date_end` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 
                COLLATE=utf8mb4_0900_ai_ci
                """
            cursor.execute(stmt)
            conn.commit()
            conn.close()
        except Error as e:
            print(e)
            raise SQLException('migrate', e.msg)

    def _connect(self):
        """ Returns a connection to MySQL Database """
        return connect(
            host=self.connection_config.get('host'),
            user=self.connection_config.get('user'),
            password=self.connection_config.get('password'),
            database=self.connection_config.get('database'),
        )
