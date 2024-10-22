<?php

namespace App\Repositories;

use App\Models\LoginCustomization;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoginCustomizationRepository
{
    protected $model;

    public function __construct(LoginCustomization $model)
    {
        $this->model = $model;
    }

    // Recupera todas as customizações
    public function getAll()
    {
        return $this->model->all();
    }

    // Recupera uma customização por ID ou lança exceção se não encontrar
    public function find($id)
    {
        
        return $this->model->findOrFail($id);
    }

    // Cria uma nova customização de login
    public function create(array $data)
    {
        // Verifica se os campos que podem ser arrays precisam ser convertidos em JSON
        if (isset($data['login_method']) && is_array($data['login_method'])) {
            $data['login_method'] = json_encode($data['login_method']);
        }
    
        if (isset($data['elements']) && is_array($data['elements'])) {
            $data['elements'] = json_encode($data['elements']);
        }
        if (isset($data['caditens']) && is_array($data['caditens'])) {
            $data['caditens'] = json_encode($data['caditens']);
        }
        if (isset($data['social_networks']) && is_array($data['social_networks'])) {
            $data['social_networks'] = json_encode($data['social_networks']);
        }
    
        return $this->model->create($data);
    }
    

    // Atualiza uma customização existente
    public function update($id, array $data)
    {
           // Verifica se os campos que podem ser arrays precisam ser convertidos em JSON
           if (isset($data['login_method']) && is_array($data['login_method'])) {
            $data['login_method'] = json_encode($data['login_method']);
        }
    
        if (isset($data['elements']) && is_array($data['elements'])) {
            $data['elements'] = json_encode($data['elements']);
        }
        if (isset($data['caditens']) && is_array($data['caditens'])) {
            $data['caditens'] = json_encode($data['caditens']);
        }
        if (isset($data['social_networks']) && is_array($data['social_networks'])) {
            $data['social_networks'] = json_encode($data['social_networks']);
        }
      //  \dd($data);
        $customization = $this->find($id); 
       // dd($data);
        $customization->update($data);
        return $customization;
    }

    // Deleta uma customização por ID
    public function delete($id)
    {
        $customization = $this->find($id); // Utilizando findOrFail

        return $customization->delete();
    }
}
