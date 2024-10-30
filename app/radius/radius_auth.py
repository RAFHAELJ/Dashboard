import sys
import logging
import radius
import json

# Configurando o logging para debug
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger("radius_auth")

def authenticate(username, password, secret, server, port=1812):
    try:
        logger.debug(f"Conectando ao servidor RADIUS {server} na porta {port} com secret '{secret}'")

        # Criando o cliente RADIUS usando py-radius
        client = radius.Radius(secret=secret, host=server, port=port, retries=3, timeout=6)

        # Enviando o pacote de autenticação
        success = client.authenticate(username, password)

        if success:
            logger.info("Autenticado com sucesso!")
            return {"success": True, "message": "Autenticado com sucesso"}
        else:
            logger.info("Falha na autenticação.")
            return {"success": False, "message": "Falha na autenticação"}
    
    except Exception as e:
        logger.error(f"Erro inesperado durante a autenticação: {e}")
        return {"success": False, "message": f"Erro inesperado: {e}"}

if __name__ == "__main__":
    if len(sys.argv) != 5:
        print("Uso: python3 radius_auth.py <username> <password> <secret> <server>")
        sys.exit(1)

    username = sys.argv[1]
    password = sys.argv[2]
    secret = sys.argv[3]
    server = sys.argv[4]

    result = authenticate(username, password, secret, server)
    print(json.dumps(result))
