<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // تشغيل RoleSeeder أولاً لإنشاء الأدوار
        $this->call(RoleSeeder::class);
        
        // ثم تشغيل UserSeeder لإنشاء المستخدمين وتعيين الأدوار لهم
        $this->call(UserSeeder::class);
    }
}
