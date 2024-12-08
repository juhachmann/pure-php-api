from http.server import BaseHTTPRequestHandler
import json, re

from tarefa.controllers import delete, edit, add, find_by_id, get_all
from tarefa.exceptions import NotFound, InvalidData
from tarefa.views import ResponseWrapper, TarefaSerializer


class APIHandler(BaseHTTPRequestHandler):
    """ HTTP Request Handler para a API
    Define rotas e métodos aceitos, resolve as rotas e envia as respostas às solicitações HTTP
    """

    def do_OPTIONS(self):
        self._send_headers(200)

    def do_GET(self):
        try:
            if self.path == "/tarefas" or self.path == "/tarefas/":
                response = get_all()
                self._send_success_response(code=200, response=response)

            elif self._is_valid_path_with_id():
                todo_id = self._id_path_param()
                response = find_by_id(todo_id)
                self._send_success_response(code=200, response=response)
            else:
                self._handle_not_found("Endpoint not found")
        except NotFound as nfe:
            self._handle_not_found(nfe.message)
        except InvalidData as inv:
            self._handle_validation_error(inv.message)
        except Exception as e:
            self._handle_generic_exception(e)

    def do_POST(self):
        try:
            if self.path == "/tarefas" or self.path == "/tarefas/":
                request_body = self._decode_request_body()
                response = add(request_body)
                self._send_success_response(code=201, response=response)
            else:
                self._handle_not_found("Endpoint not found")
        except InvalidData as inv:
            self._handle_validation_error(inv.message)
        except Exception as e:
            self._handle_generic_exception(e)

    def do_PUT(self):
        try:
            if self._is_valid_path_with_id():
                todo_id = self._id_path_param()
                request_body = self._decode_request_body()
                response = edit(todo_id, request_body)
                self._send_success_response(code=200, response=response)
            else:
                self._handle_not_found("Endpoint not found")
        except NotFound as nfe:
            self._handle_not_found(nfe.message)
        except InvalidData as inv:
            self._handle_validation_error(inv.message)
        except Exception as e:
            self._handle_generic_exception(e)

    def do_DELETE(self):
        try:
            if self._is_valid_path_with_id():
                todo_id = self._id_path_param()
                delete(todo_id)
                self._send_success_response(code=204, response=None)
            else:
                self._handle_not_found("Endpoint not found")
        except NotFound as nfe:
            self._handle_not_found(nfe.message)
        except Exception as e:
            self._handle_generic_exception(e)


### Métodos privados auxiliares

    def _send_headers(self, code: int) -> None:
        """Ações de cabeçalho de resposta"""
        self.send_response(code)
        self.send_header('Content-Type', 'application/json')
        self._send_cors_headers()
        self.end_headers()
        
    def _send_cors_headers(self):
      """ Define headers necessários para CORS """
      self.send_header("Access-Control-Allow-Origin", "*")
      self.send_header("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS")
      self.send_header("Access-Control-Allow-Headers", "x-api-key,Content-Type")

    def _send_success_response(self, response: ResponseWrapper | None, code: int) -> None:
        """ Mensagem de sucesso """
        self._send_headers(code)
        if response:
            self.wfile.write(json.dumps(response, cls=TarefaSerializer).encode('utf-8'))

    def _is_valid_path_with_id(self) -> re.Match[str] | None:
        """ Verifica se o path segue o padrão '/tarefas/2' """
        pattern = r"^/tarefas/\d+$"
        return re.fullmatch(pattern, self.path)

    def _id_path_param(self) -> int:
        """ Retorna apenas o parâmetro ID do caminho. Exemplo:
         path: /tarefas/2
         ID: 2
         """
        return int(self.path.split('/')[-1])

    def _decode_request_body(self) -> dict:
        """ Lê o request body e o transforma em um dicionário """
        content_length = int(self.headers['Content-Length'])
        body = self.rfile.read(content_length)
        return json.loads(body.decode())

    def _handle_not_found(self, error: str) -> None:
        """ Resposta para recursos não encontrados (404 Not Found) """
        self._send_headers(404)
        response = ResponseWrapper(status="error", code=404, message=error)
        self.wfile.write(json.dumps(response, cls=TarefaSerializer).encode('utf-8'))

    def _handle_validation_error(self, error: str | list) -> None:
        """ Resposta para erros de validação (400 Bad Request) """
        self._send_headers(400)
        response = ResponseWrapper(status="fail", code=400, message=error)
        self.wfile.write(json.dumps(response, cls=TarefaSerializer).encode('utf-8'))

    def _handle_generic_exception(self, error: Exception) -> None:
        """ Resposta para erros internos da aplicação (500 Internal Server Error) """
        print(error)
        self._send_headers(500)
        response  = ResponseWrapper(status="error", code=500, message="An error has occurred in our server.")
        self.wfile.write(json.dumps(response, cls=TarefaSerializer).encode('utf-8'))

