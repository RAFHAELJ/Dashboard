<?php

// app/Repositories/UserRepository.php
namespace App\Repositories;

use App\Models\User;


class UserRepository{

    public function all() {
        return User::paginate();
    }
    public function count() {
        return User::all()->count();
    }

    public function find($id) {
        return User::find($id);
    }

    public function create(array $data) {
        //dd($data);
        return User::create($data);
    }

    public function update(Request $request, User $user) {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
          
        ]);
        return $user;
    }

    public function delete($id) {
        return User::destroy($id);
    }
}
