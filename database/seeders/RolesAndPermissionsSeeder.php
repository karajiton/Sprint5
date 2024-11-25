<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        
        // Crear permisos
        Permission::create(['name' => 'manage games']);
        Permission::create(['name' => 'play games']);

        // Crear roles
        $admin = Role::create(['name' => 'admin']);
        $player = Role::create(['name' => 'player']);

        // Asignar permisos a roles
        $admin->givePermissionTo('manage games');
        $player->givePermissionTo('play games');
    }
}
