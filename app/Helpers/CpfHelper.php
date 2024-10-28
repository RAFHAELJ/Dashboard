<?php

namespace App\Helpers;

class CpfHelper
{
    /**
     * Limpa o CPF, removendo caracteres não numéricos.
     *
     * @param string $cpf
     * @return string
     */
    public static function clearNumbers($cpf)
    {
        return preg_replace('/\D/', '', $cpf);
    }

    /**
     * Valida o CPF.
     *
     * @param string $cpf
     * @return bool
     */
    public static function validaCPF($cpf)
    {
        // Limpar caracteres não numéricos
        $cpf = self::clearNumbers($cpf);

        // Verificar se o CPF tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verificar se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcular os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gera a senha PAP.
     *
     * @param string $challenge
     * @param string $uamsecret
     * @param string $password
     * @return string
     */
    public static function papPass($challenge, $uamsecret, $password)
    {
        // Verifica se o challenge tem 32 caracteres, caso contrário, adiciona zeros à direita
        $challenge = str_pad($challenge, 32, '0', STR_PAD_RIGHT);
    
        // Verifica se o challenge está no formato hexadecimal
        if (strlen($challenge) !== 32 || !ctype_xdigit($challenge)) {
            return null; // Retorna null ou lança uma exceção, caso o formato seja inválido
        }
    
        // Converte o challenge e o secret para o formato correto
        $hexchal = pack("H32", $challenge);
        $newchal = pack("H*", md5($hexchal . $uamsecret));
        $newpwd = pack("a32", $password);
        $pappassword = implode("", unpack("H32", ($newpwd ^ $newchal)));
    
        return $pappassword;
    }
    
}
