<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        $user->profile()->create([
            'name' => 'Super Admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin Super No. 1',
        ]);
    }
}