import os
from dotenv import load_dotenv
from server import run

from api_handler import APIHandler
from tarefa.models import TarefaModel
from tarefa.repository_mysql import TarefaRepositoryMySQL

""" Configurações e inicializações para a aplicação """

# Carrega variáveis de ambiente a partir do arquivo .env
load_dotenv()

# Lê a porta
port = int(os.getenv("PORT"))

# Lê as propriedades de configuração de banco de dados MYSQL
connection_config = {
    'host'      : os.getenv("DB_HOST_MYSQL"),
    'user'      : os.getenv("DB_USER_MYSQL"),
    'password'  : os.getenv("DB_PASSWORD_MYSQL"),
    'database'  : os.getenv("DB_DATABASE_MYSQL")
}

if __name__ == "__main__":
    # Instancia e injeta a implementação de repositório para MySQL no modelo
    repository = TarefaRepositoryMySQL(connection_config)
    TarefaModel.set_repository(repository)

    # Cria a tabela
    repository.migrate()

    # Serve forever
    run(port=port, handler_class=APIHandler)
