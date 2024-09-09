<?php

namespace App\Repositories;

use App\Models\LoginCustomization;

class LoginCustomizationRepository
{
    protected $model;

    public function __construct(LoginCustomization $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $customization = $this->model->find($id);
        if ($customization) {
            $customization->update($data);
            return $customization;
        }
        return null;
    }
}
