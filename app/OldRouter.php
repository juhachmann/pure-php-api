<?php

namespace app;

//use app\Exceptions\NotFoundException;
//use app\Exceptions\ValidationException;

use app\controllers\TarefaController;
use app\exceptions\NotFoundException;
use app\exceptions\ValidationException;
use Exception;

class OldRouter {

    private TarefaController $controller;

    public function __construct()
    {
        $this->controller = new TarefaController();
    }

    public function resolve(string $uri, string $method): void
    {
         if ($uri === '/tarefas') {
                if ($method === 'GET') {
                    $response = $this->controller->getAll();
                    $this->send_response($response, 200);
                } elseif ($method === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $response = $this->controller->create($input);
                    $this->send_response($response, 201);
                } else {
                    $this->send_response("Método não suportado", 400);
                }
            } elseif (preg_match('/^tarefas\/(\d+)$/', $uri, $matches)) {
                $id = $matches[1];
                if ($method === 'GET') {
                    $response = $this->controller->getOne($id);
                    $this->send_response($response, 200);
                } elseif ($method === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $response = $this->controller->update($id, $input);
                    $this->send_response($response, 200);
                } elseif ($method === 'DELETE') {
                    $this->controller->delete($id);
                    $this->send_response(null, 204);
                } else {
                    $this->send_response("Método não suportado", 400);
                }
            } else {
                $this->send_response('Endpoint não encontrado', 404);
            }
        }


    private function send_response(mixed $message, int $code) : void {
        http_response_code($code);
        header('Content-Type: application/json');
        if($message != null)
            echo json_encode($message);
    }

    private function handle_exception(Exception $ex): void
    {
        if($ex instanceof NotFoundException) {
            $this->send_response($ex->getMessage(), 404);
            return;
        }
        if($ex instanceof ValidationException) {
            $this->send_response($ex->getMessage(), 400);
            return;
        }
        $this->send_response("Ocorreu um erro em nossos servidores", 500);
    }

}


//    def do_OPTIONS(self):
//        self._send_headers(200)
//
//    def do_GET(self):
//        try:
//            if self.path == "/tarefas" or self.path == "/tarefas/":
//                response = get_all()
//                self._send_success_response(code=200, response=response)
//
//            elif self._is_valid_path_with_id():
//                todo_id = self._id_path_param()
//                response = find_by_id(todo_id)
//                self._send_success_response(code=200, response=response)
//            else:
//                self._handle_not_found("Endpoint not found")
//        except NotFound as nfe:
//            self._handle_not_found(nfe.message)
//        except InvalidData as inv:
//            self._handle_validation_error(inv.message)
//        except Exception as e:
//            self._handle_generic_exception(e)
//
//    def do_POST(self):
//        try:
//            if self.path == "/tarefas" or self.path == "/tarefas/":
//                request_body = self._decode_request_body()
//                response = add(request_body)
//                self._send_success_response(code=201, response=response)
//            else:
//                self._handle_not_found("Endpoint not found")
//        except InvalidData as inv:
//            self._handle_validation_error(inv.message)
//        except Exception as e:
//            self._handle_generic_exception(e)
//
//    def do_PUT(self):
//        try:
//            if self._is_valid_path_with_id():
//                todo_id = self._id_path_param()
//                request_body = self._decode_request_body()
//                response = edit(todo_id, request_body)
//                self._send_success_response(code=200, response=response)
//            else:
//                self._handle_not_found("Endpoint not found")
//        except NotFound as nfe:
//            self._handle_not_found(nfe.message)
//        except InvalidData as inv:
//            self._handle_validation_error(inv.message)
//        except Exception as e:
//            self._handle_generic_exception(e)
//
//    def do_DELETE(self):
//        try:
//            if self._is_valid_path_with_id():
//                todo_id = self._id_path_param()
//                delete(todo_id)
//                self._send_success_response(code=204, response=None)
//            else:
//                self._handle_not_found("Endpoint not found")
//        except NotFound as nfe:
//            self._handle_not_found(nfe.message)
//        except Exception as e:
//            self._handle_generic_exception(e)
//
//
//### Métodos privados auxiliares
//
//    def _send_headers(self, code: int) -> None:
//        """Ações de cabeçalho de resposta"""
//        self.send_response(code)
//        self.send_header('Content-Type', 'application/json')
//        self._send_cors_headers()
//        self.end_headers()
//
//    def _send_cors_headers(self):
//      """ Define headers necessários para CORS """
//      self.send_header("Access-Control-Allow-Origin", "*")
//      self.send_header("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS")
//      self.send_header("Access-Control-Allow-Headers", "x-api-key,Content-Type")
//
//    def _send_success_response(self, response: ResponseWrapper | None, code: int) -> None:
//        """ Mensagem de sucesso """
//        self._send_headers(code)
//        if response:
//            self.wfile.write(json.dumps(response, cls=TarefaSerializer).encode('utf-8'))
//
//    def _is_valid_path_with_id(self) -> re.Match[str] | None:
//        """ Verifica se o path segue o padrão '/tarefas/2' """
//        pattern = r"^/tarefas/\d+$"
//        return re.fullmatch(pattern, self.path)
//
//    def _id_path_param(self) -> int:
//        """ Retorna apenas o parâmetro ID do caminho. Exemplo:
//         path: /tarefas/2
//         ID: 2
//         """
//        return int(self.path.split('/')[-1])
//
//    def _decode_request_body(self) -> dict:
//        """ Lê o request body e o transforma em um dicionário """
//        content_length = int(self.headers['Content-Length'])
//        body = self.rfile.read(content_length)
//        return json.loads(body.decode())
//
//    def _handle_not_found(self, error: str) -> None:
//        """ Resposta para recursos não encontrados (404 Not Found) """
//        self._send_headers(404)
//        response = ResponseWrapper(status="error", code=404, message=error)
//        self.wfile.write(json.dumps(response, cls=TarefaSerializer).encode('utf-8'))
//
//    def _handle_validation_error(self, error: str | list) -> None:
//        """ Resposta para erros de validação (400 Bad Request) """
//        self._send_headers(400)
//        response = ResponseWrapper(status="fail", code=400, message=error)
//        self.wfile.write(json.dumps(response, cls=TarefaSerializer).encode('utf-8'))
//
//    def _handle_generic_exception(self, error: Exception) -> None:
//        """ Resposta para erros internos da aplicação (500 Internal Server Error) """
//        print(error)
//        self._send_headers(500)
//        response  = ResponseWrapper(status="error", code=500, message="An error has occurred in our server.")
//        self.wfile.write(json.dumps(response, cls=TarefaSerializer).encode('utf-8'))
//
