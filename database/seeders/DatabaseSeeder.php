<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name'  => 'administrador',
                'email' => 'administrador@parqueadero.com',
                'role'  => 'administrador',
            ],
            [
                'name'  => 'operador_cabina',
                'email' => 'operador_cabina@parqueadero.com',
                'role'  => 'operador_cabina',
            ],
            [
                'name'  => 'cajero',
                'email' => 'cajero@parqueadero.com',
                'role'  => 'cajero',
            ],
            [
                'name'  => 'supervisor_salida',
                'email' => 'supervisor_salida@parqueadero.com',
                'role'  => 'supervisor_salida',
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::updateOrCreate(
                ['email' => $usuario['email']],
                [
                    'name'     => $usuario['name'],
                    'password' => Hash::make('12345678'),
                    'role'     => $usuario['role'],
                ]
            );
        }
    }
}