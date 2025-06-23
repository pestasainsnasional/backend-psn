<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
       $this->call([
            RolePermissionSeeder::class,
        ]);

        $user = User::updateOrCreate(
            ['email' => env('APP_FILAMENT_USER')], 
            [
                'name' => 'Admin PSN', 
                'password' => bcrypt(env('APP_FILAMENT_PASSWORD')),
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('admin');
    }
}
