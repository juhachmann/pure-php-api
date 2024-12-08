from http.server import HTTPServer, BaseHTTPRequestHandler

""" HTTP Server """

def run(server_class=HTTPServer, handler_class=BaseHTTPRequestHandler, port=8000):
    server_address = ('', port)
    httpd = server_class(server_address, handler_class)
    print(f"Server running on port {port}")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    finally:
        httpd.server_close()
        print(f'\nStopping server on port {port}...')

