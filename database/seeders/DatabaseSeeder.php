<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $existingUser = User::where('email', env('APP_FILAMENT_USER'))->first();
        
        if (!$existingUser) {
            $user = User::create([
                'name'              => 'Admin PSN',
                'email'             => env('APP_FILAMENT_USER'),
                'password'          => env('APP_FILAMENT_PASSWORD'), 
                'email_verified_at' => now(),
            ]);
            
            $user->assignRole('admin');
            
            $this->command->info('Super admin user created successfully!');
            $this->command->info('Email: ' . env('APP_FILAMENT_USER'));
        } else {
            $this->command->info('Super admin user already exists!');
        }
    }
}