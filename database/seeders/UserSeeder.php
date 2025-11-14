<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@digitaledge.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('123'),
                'email_verified_at' => now(),
            ]
        );
        
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }
        
        $normalUser = User::updateOrCreate(
            ['email' => 'user@digitaledge.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('123'),
                'email_verified_at' => now(),
            ]
        );
        
        if (!$normalUser->hasRole('user')) {
            $normalUser->assignRole('user');
        }

    }
}
