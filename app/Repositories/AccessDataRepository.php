<?php

namespace App\Repositories;

use App\Models\AccessData;
use Illuminate\Http\Request;

class AccessDataRepository
{
    protected $model;

    public function __construct(AccessData $accessData)
    {
        $this->model = $accessData;
    }

    public function all(Request $request ,int $per_page)
    {
       
        return $this->model->paginate($per_page);
    }

    public function findByType($type,int $per_page)
    {
        return $this->model->where('type', $type)->with('regiao')->paginate($per_page);
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
