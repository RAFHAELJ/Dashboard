<?php
namespace App\Repositories;

use App\Models\Card;
use Illuminate\Http\Request;

interface CardRepositoryInterface
{
    public function all();

    public function find($id);

    public function create(Request $request);

    public function update($id, array $data);

    public function delete($id);
}
