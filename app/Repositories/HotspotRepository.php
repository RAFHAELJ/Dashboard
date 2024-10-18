<?php

namespace App\Repositories;

use App\Models\Radcheck;
use App\Helpers\CpfHelper;
use App\Models\LoginCustomization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HotspotRepository
{

    public function login($regiao){

        return LoginCustomization::where('region',$regiao)->get();
    }
    public function registerUser(array $data, $region)
    {
        // Limpar números de CPF, telefone, etc.
        $cpf = CpfHelper::clearNumbers($data['cpf']);
        $telefone = CpfHelper::clearNumbers($data['telefone']);

        // Validação de CPF
        if ($cpf && !CpfHelper::validaCPF($cpf)) {
            return ['success' => false, 'error' => 'CPF inválido'];
        }

        // Verificação de usuário existente no `radcheck`
        $existingUser = Radcheck::where('UserName', $data['username'])->first();
        if ($existingUser) {
            return ['success' => false, 'error' => 'Este usuário já está registrado'];
        }

        // Criar usuário no RADIUS
        $radcheck = new Radcheck();
        $radcheck->UserName = $data['username'];
        $radcheck->Attribute = 'Cleartext-Password';
        $radcheck->op = ':=';
        $radcheck->Value = $data['password'];
        $radcheck->data_criacao = now();
        $radcheck->ultimo_acesso = now();
        $radcheck->cpf = $cpf;
        $radcheck->telefone = $telefone;
        $radcheck->email = $data['email'];
        $radcheck->regiao = $region;  // Atribuímos a região ao registro do usuário
        $radcheck->save();

        // Geração de senha PAP
        $pappassword = CpfHelper::papPass(Session::get('challenge'), config('radius.uamsecret'), $data['password']);

        // Definir URL de redirecionamento com UAM
        $uamip = Session::get('uamip');
        $uamport = Session::get('uamport');
        $url = $uamip ? 'http://' . $uamip . ':' . $uamport . '/logon?username=' . urlencode($radcheck->UserName) . '&password=' . urlencode($pappassword)
                      : Session::get('linklogin') . '?username=' . urlencode($radcheck->UserName) . '&password=' . urlencode($radcheck->Value);

        return ['success' => true, 'url' => $url];
    }

    public function authenticateUser(array $data, $region)
    {
        // Autenticar o usuário na tabela `radcheck`
        $user = Radcheck::where('UserName', $data['username'])
            ->where('Value', $data['password'])
            ->where('regiao', $region)  // Filtrar pela região
            ->first();

        if (!$user) {
            return ['success' => false, 'error' => 'Usuário ou senha inválidos'];
        }

        // Atualizar o último acesso
        $user->ultimo_acesso = now();
        $user->save();

        // Geração de senha PAP
        $pappassword = CpfHelper::papPass(Session::get('challenge'), config('radius.uamsecret'), $data['password']);

        // Definir URL de redirecionamento com UAM
        $uamip = Session::get('uamip');
        $uamport = Session::get('uamport');
        $url = $uamip ? 'http://' . $uamip . ':' . $uamport . '/logon?username=' . urlencode($user->UserName) . '&password=' . urlencode($pappassword)
                      : Session::get('linklogin') . '?username=' . urlencode($user->UserName) . '&password=' . urlencode($user->Value);

        return ['success' => true, 'url' => $url];
    }
}
