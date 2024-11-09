<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Rafhael',
            'email' => 'janke.irineu@gmail.com',
            'nomeCliente' => null,
            'nivel' => 'Administrador',
            'email_verified_at' => now(),
            'password' => Hash::make('wnidobrasil'),
            'regiao' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
