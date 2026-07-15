<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@ocp.ma'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('Admin1234!'),
                'role'     => 'admin',
            ]
        );

        // Manager
        User::updateOrCreate(
            ['email' => 'manager@ocp.ma'],
            [
                'name'     => 'Manager OCP',
                'password' => Hash::make('Manager1234!'),
                'role'     => 'manager',
            ]
        );

        // Agent
        User::updateOrCreate(
            ['email' => 'agent@ocp.ma'],
            [
                'name'     => 'Agent Laverie',
                'password' => Hash::make('Agent1234!'),
                'role'     => 'agent',
            ]
        );
    }
}
