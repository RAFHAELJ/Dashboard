<?php

namespace App\Repositories;

use App\Models\AccessData;

class AccessDataRepository
{
    protected $model;

    public function __construct(AccessData $accessData)
    {
        $this->model = $accessData;
    }

    public function all()
    {
       
        return $this->model->paginate(15);
    }

    public function findByType($type)
    {
        return $this->model->where('type', $type)->with('regiao')->paginate(15);
    }

    public function create(array $data)
    {
        //\dd($data);
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $accessData = $this->model->findOrFail($id);
        $accessData->update($data);
        return $accessData;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

        // Método para contar o total de bancos de dados
        public function getTotalDatabases()
        {
            return $this->model->where('type', 'database')->count();
        }
    
        // Método para contar o total de controladoras
        public function getTotalControllers()
        {
            return $this->model->where('type', 'controller')->count();
        }
}
