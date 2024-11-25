<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Crear roles con el guard 'api'
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $player = Role::create(['name' => 'player', 'guard_name' => 'api']);

        // Crear permisos con el guard 'api'
        Permission::create(['name' => 'manage games', 'guard_name' => 'api']);
        Permission::create(['name' => 'play games', 'guard_name' => 'api']);

        // Asignar permisos a roles
        $admin->givePermissionTo(['manage games', 'play games']);
        $player->givePermissionTo(['play games']);
    }
}
