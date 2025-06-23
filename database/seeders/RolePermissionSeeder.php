<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::updateOrCreate([
            'name' => 'admin'
        ]);
        $user = Role::updateOrCreate([
            'name' => 'user'
        ]);
    }
}

