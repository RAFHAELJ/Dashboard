import sys
import logging
import json
from pyrad.client import Client
from pyrad.packet import AccessRequest, AccessAccept, AccessReject
from pyrad.dictionary import Dictionary

# Configuração do logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger("radius_auth")

# Defina o caminho do arquivo de dicionário
dictionary_path = "/var/www/html/Dashboard/app/radius/dictionary"

def authenticate(username, password, secret, server, port=1812):
    try:
        logger.debug(f"Conectando ao servidor RADIUS {server} na porta {port} com secret '{secret}'")

        # Configuração do cliente RADIUS com pyrad, usando o arquivo de dicionário
        client = Client(server=server, secret=secret.encode(), dict=Dictionary(dictionary_path))
        client.auth_port = port

        # Criação do pacote de requisição de autenticação
        req = client.CreateAuthPacket(code=AccessRequest)
        req["User-Name"] = username
        req["User-Password"] = req.PwCrypt(password)
        req["NAS-IP-Address"] = "127.0.0.1"  # Ajuste conforme necessário

        # Envio do pacote e recebimento da resposta
        reply = client.SendPacket(req)

        if reply is None:
            raise ValueError("Nenhuma resposta recebida do servidor RADIUS")

        if reply.code == AccessAccept:
            logger.info("Autenticado com sucesso!")
            return {"success": True, "message": "Autenticado com sucesso"}
        elif reply.code == AccessReject:
            logger.info("Falha na autenticação.")
            return {"success": False, "message": "Falha na autenticação"}
        else:
            logger.error("Resposta inesperada do servidor RADIUS.")
            return {"success": False, "message": "Resposta inesperada do servidor RADIUS"}
    except Exception as e:
        # Captura da exceção com mais detalhes
        error_message = f"Erro inesperado durante a autenticação: {e} - Tipo de erro: {type(e).__name__}"
        logger.error(error_message, exc_info=True)
        return {"success": False, "message": error_message}

if __name__ == "__main__":
    if len(sys.argv) != 5:
        response = {
            "success": False,
            "message": "Uso incorreto: python3 radius_auth.py <username> <password> <secret> <server>"
        }
        print(json.dumps(response))
        sys.exit(1)

    username = sys.argv[1]
    password = sys.argv[2]
    secret = sys.argv[3]
    server = sys.argv[4]

    result = authenticate(username, password, secret, server)
    print(json.dumps(result))
