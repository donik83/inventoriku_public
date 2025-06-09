<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar; // Untuk reset cache

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Cipta Permissions (Kebenaran) - Sesuaikan mengikut keperluan Tuan
        $this->command->info('Creating Permissions...');
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage roles']);
        Permission::create(['name' => 'manage categories']);
        Permission::create(['name' => 'manage locations']);
        Permission::create(['name' => 'view items']);
        Permission::create(['name' => 'create items']);
        Permission::create(['name' => 'edit items']);   // Kebenaran umum edit
        Permission::create(['name' => 'delete items']); // Kebenaran umum padam
        Permission::create(['name' => 'record movements']);
        Permission::create(['name' => 'view reports']);
        // Tambah lagi jika perlu, cth: 'edit own items', 'delete own items'

        // Cipta Roles (Peranan)
        $this->command->info('Creating Roles...');
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']); // Contoh nama, boleh tukar ke 'Ahli' dll.

        // Berikan SEMUA permissions kepada Role Admin
        $adminRole->givePermissionTo(Permission::all());
        $this->command->info('Admin role granted all permissions.');

        // Berikan permissions spesifik kepada Role User (Contoh)
        $userRole->givePermissionTo([
            'view items',
            'create items',
            // 'edit items', // Mungkin User biasa tak boleh edit semua?
            // 'delete items', // Mungkin User biasa tak boleh padam semua?
            'record movements',
            'view reports',
        ]);
        $this->command->info('User role granted specific permissions.');

         $this->command->info('Roles and Permissions seeded successfully.');
    }
}
