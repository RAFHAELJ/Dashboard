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
        return $this->model->where('type', $type)->paginate(15);
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
}
