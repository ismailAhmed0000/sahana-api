<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sahana.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'approved' => true,
                'is_admin' => true,
            ]
        );
    }
}
