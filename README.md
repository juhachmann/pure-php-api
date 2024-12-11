# Agenda de Tarefas - Backend

Backend em PHP para um gerenciador de tarefas. Desenvolvido como uma API para consumo pelo frontend.

## Instalação

### Requisitos: 

- PHP
- composer

### Passo a passo:

A partir da pasta raiz do projeto, instale as dependências:

```bash
composer install
```

Altere o arquivo `.env` inserindo as configurações do banco de dados MySQL e a porta a ser usada pela aplicação:

```
PORT=8000

DB_HOST_MYSQL=localhost
DB_USER_MYSQL=user
DB_PASSWORD_MYSQL=password
DB_DATABASE_MYSQL=todo
```

A partir da pasta `public` onde está localizado o arquivo `index.php`, inicie o servidor.
As tabelas do banco de dados serão criadas neste momento, se necessário.

```bash
php -S localhost:8000
```


## Endpoints


| Método | Endpoint                    | Descrição               | Request Body                      | 
|--------|-----------------------------|-------------------------|-----------------------------------|
| POST   | /tarefas                    | criar nova tarefa       | [Tarefa](#Tarefa) (sem id)        | 
| GET    | /tarefas                    | ver todas as tarefas    |                                   |
| GET    | /tarefas/{id}               | encontrar tarefa por id |                                   | 
| PUT    | /tarefas/{id}               | editar uma tarefa       | [Tarefa](#Tarefa) (com ou sem id) | 
| DELETE | /tarefas/{id}               | deletar uma tarefa       |                                   | 

### Tarefa

```json
{
  "id" : 1,
  "title": "Ler artigo",
  "description": "Ler artigo para dissertação",
  "status": "done",
  "date_start": "2024-12-01",
  "date_end": "2024-12-05"
}
```

### ResponseBody

Respostas no padrão [JSend](https://github.com/omniti-labs/jsend)

