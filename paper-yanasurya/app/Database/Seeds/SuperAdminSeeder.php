<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Seed Roles
        $roles = [
            [
                'id'          => 1,
                'name'        => 'Superadmin',
                'description' => 'Akses penuh ke seluruh modul sistem SCM dan Invoice Paper Yana Surya',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 2,
                'name'        => 'Finance',
                'description' => 'Akses pengelolaan invoice, pelunasan pembayaran, piutang & hutang',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 3,
                'name'        => 'Procurement',
                'description' => 'Akses pengelolaan Purchase Order (PO) dan relasi vendor/supplier',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 4,
                'name'        => 'Warehouse',
                'description' => 'Akses manajemen stok gudang, mutasi masuk/keluar, dan penerimaan barang',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 5,
                'name'        => 'Sales',
                'description' => 'Akses penerbitan invoice pelanggan dan pengelolaan data pelanggan',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        foreach ($roles as $role) {
            $existing = $this->db->table('roles')->where('name', $role['name'])->get()->getRow();
            if (! $existing) {
                $this->db->table('roles')->insert($role);
            }
        }

        // Seed Superadmin and Staff Users
        $defaultPassword = password_hash('password123', PASSWORD_BCRYPT);

        $users = [
            [
                'role_id'       => 1,
                'username'      => 'superadmin',
                'email'         => 'admin@yanasurya.com',
                'password_hash' => $defaultPassword,
                'full_name'     => 'Super Admin Yana Surya',
                'phone'         => '081234567890',
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'role_id'       => 2,
                'username'      => 'finance',
                'email'         => 'finance@yanasurya.com',
                'password_hash' => $defaultPassword,
                'full_name'     => 'Siti Rahmawati (Finance)',
                'phone'         => '081298765432',
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'role_id'       => 4,
                'username'      => 'warehouse',
                'email'         => 'warehouse@yanasurya.com',
                'password_hash' => $defaultPassword,
                'full_name'     => 'Budi Santoso (Warehouse)',
                'phone'         => '081345678901',
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        foreach ($users as $user) {
            $existing = $this->db->table('users')->where('username', $user['username'])->get()->getRow();
            if (! $existing) {
                $this->db->table('users')->insert($user);
            }
        }
    }
}
