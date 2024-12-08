# Agenda de Tarefas - Backend

Backend em Python para um gerenciador de tarefas

## Instalação

### Requisitos: 

- Python3+
- pip

### Passo a passo:

Na pasta raiz do projeto, crie um ambiente virtual do python:

```bash
python3 -m venv venv
```

Ative o ambiente virtual que foi criado:

Windows:

```bash
# In cmd.exe
venv\Scripts\activate.bat
# In PowerShell
venv\Scripts\Activate.ps1
```

Linux / MacOS:

```bash
source venv/bin/activate
```

A indicação do ambiente virtual deve aparecer no seu terminal de comando, como em:

```bash
(venv) user@user: $ source venv/bin/activate
```

Com o ambiente virtual ativo, instale as dependências:

```bash
pip install -r requirements.txt
```

Altere o arquivo `.env` inserindo as configurações do banco de dados MySQL e a porta a ser usada pela aplicação:

```
PORT=8000

DB_HOST_MYSQL=localhost
DB_USER_MYSQL=user
DB_PASSWORD_MYSQL=password
DB_DATABASE_MYSQL=todo_python
```

Inicie a aplicação a partir do arquivo `launch.py`. A tabela do banco de dados será criada neste momento

```bash
python3 launch.py
```

