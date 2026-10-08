<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name'              => 'Super Admin',
                'full_name'         => 'Super Administrator',
                'email'             => 'admin@english-today.com',
                'password'          => Hash::make('password123'),
                'role'              => 'hr',
                'status'            => 'active',
                'division_id'       => null,
                'phone'             => '081234567890',
                'address'           => 'Administrator Office',
                'avatar'            => null,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Admin',
                'full_name'         => 'Admin English Today',
                'email'             => 'admin@etquizzes.com',
                'password'          => Hash::make('password123'),
                'role'              => 'hr',
                'status'            => 'active',
                'division_id'       => null,
                'phone'             => '081234567891',
                'address'           => 'Administrator Office',
                'avatar'            => null,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'HR Admin',
                'full_name'         => 'HR Administrator',
                'email'             => 'hr@etquizzes.com',
                'password'          => Hash::make('password123'),
                'role'              => 'hr',
                'status'            => 'active',
                'division_id'       => null,
                'phone'             => '081234567892',
                'address'           => 'HR Department',
                'avatar'            => null,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
            $this->command->info("Admin user [{$user->email}] with role [{$user->role}] created/updated successfully.");
        }
    }
}
