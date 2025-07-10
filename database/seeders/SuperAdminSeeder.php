<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
// 1. Buat role jika belum ada
        $roles = ['super_admin', 'hr', 'spv', 'staff'];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // 2. Ambil role_id untuk super_admin
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();

        if (!$superAdminRole) {
            throw new \Exception("Role 'super_admin' tidak ditemukan.");
        }

        // 3. Hapus user lama jika ada (opsional)
        DB::table('users')->where('username', 'admin')->delete();

        // 4. Buat super admin user
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    }
