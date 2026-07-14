<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\QuizCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create divisions sesuai yang diminta
        $divisions = [
            ['name' => 'IT', 'description' => 'Information Technology Division - Handling all technical and digital infrastructure'],
            ['name' => 'KPJ', 'description' => 'Keselamatan dan Pengawasan Jaringan - Network Safety and Monitoring'],
            ['name' => 'LPS', 'description' => 'Lembaga Pembinaan dan Pengembangan - Training and Development Institution'],
            ['name' => 'MEDIA', 'description' => 'Media Division - Content creation, documentation, and publication'],
            ['name' => 'PENDIDIKAN', 'description' => 'Education Division - Academic and educational programs'],
            ['name' => 'PKA', 'description' => 'Pemuda dan Kreativitas - Youth and Creative Division'],
            ['name' => 'RG', 'description' => 'Research and Development - Research and Innovation Division'],
            ['name' => 'SAPRAS', 'description' => 'Sarana dan Prasarana - Facilities and Infrastructure Division'],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }

        // Create quiz categories
        $categories = [
            ['name' => 'Grammar', 'description' => 'English grammar quizzes'],
            ['name' => 'Vocabulary', 'description' => 'English vocabulary quizzes'],
            ['name' => 'Reading Comprehension', 'description' => 'Reading comprehension tests'],
            ['name' => 'Listening', 'description' => 'Listening comprehension quizzes'],
            ['name' => 'Writing', 'description' => 'Writing skills assessment'],
            ['name' => 'Speaking', 'description' => 'Speaking and pronunciation quizzes'],
            ['name' => 'Business English', 'description' => 'Business English communication'],
            ['name' => 'TOEFL Preparation', 'description' => 'TOEFL test preparation quizzes'],
        ];

        foreach ($categories as $category) {
            QuizCategory::create($category);
        }

        // Create HR Admin account
        User::create([
            'name' => 'HR Admin',
            'full_name' => 'HR Administrator',
            'email' => 'hr@etquizzes.com',
            'password' => Hash::make('password123'),
            'role' => 'hr',
            'status' => 'active',
            'division_id' => null, // HR tidak punya divisi spesifik
            'phone' => '081234567890',
            'address' => 'HR Department, Main Office',
        ]);

        // Create employees for each division
        $employees = [
            // IT Division
            [
                'name' => 'Agus IT',
                'full_name' => 'Agus Setiawan',
                'email' => 'agus.it@etquizzes.com',
                'division' => 'IT',
                'phone' => '081234567891',
                'address' => 'IT Department, Floor 3'
            ],
            [
                'name' => 'Budi IT',
                'full_name' => 'Budi Santoso',
                'email' => 'budi.it@etquizzes.com',
                'division' => 'IT',
                'phone' => '081234567892',
                'address' => 'IT Department, Floor 3'
            ],

            // KPJ Division
            [
                'name' => 'Citra KPJ',
                'full_name' => 'Citra Rahayu',
                'email' => 'citra.kpj@etquizzes.com',
                'division' => 'KPJ',
                'phone' => '081234567893',
                'address' => 'KPJ Department, Floor 2'
            ],
            [
                'name' => 'Dedi KPJ',
                'full_name' => 'Dedi Purnomo',
                'email' => 'dedi.kpj@etquizzes.com',
                'division' => 'KPJ',
                'phone' => '081234567894',
                'address' => 'KPJ Department, Floor 2'
            ],

            // LPS Division
            [
                'name' => 'Eka LPS',
                'full_name' => 'Eka Permata',
                'email' => 'eka.lps@etquizzes.com',
                'division' => 'LPS',
                'phone' => '081234567895',
                'address' => 'LPS Department, Floor 4'
            ],
            [
                'name' => 'Fajar LPS',
                'full_name' => 'Fajar Nugroho',
                'email' => 'fajar.lps@etquizzes.com',
                'division' => 'LPS',
                'phone' => '081234567896',
                'address' => 'LPS Department, Floor 4'
            ],

            // MEDIA Division
            [
                'name' => 'Gita MEDIA',
                'full_name' => 'Gita Puspita',
                'email' => 'gita.media@etquizzes.com',
                'division' => 'MEDIA',
                'phone' => '081234567897',
                'address' => 'MEDIA Department, Floor 1'
            ],
            [
                'name' => 'Hadi MEDIA',
                'full_name' => 'Hadi Wijaya',
                'email' => 'hadi.media@etquizzes.com',
                'division' => 'MEDIA',
                'phone' => '081234567898',
                'address' => 'MEDIA Department, Floor 1'
            ],

            // PENDIDIKAN Division
            [
                'name' => 'Indah PENDIDIKAN',
                'full_name' => 'Indah Sari',
                'email' => 'indah.pendidikan@etquizzes.com',
                'division' => 'PENDIDIKAN',
                'phone' => '081234567899',
                'address' => 'PENDIDIKAN Department, Floor 5'
            ],
            [
                'name' => 'Joko PENDIDIKAN',
                'full_name' => 'Joko Susilo',
                'email' => 'joko.pendidikan@etquizzes.com',
                'division' => 'PENDIDIKAN',
                'phone' => '081234567900',
                'address' => 'PENDIDIKAN Department, Floor 5'
            ],

            // PKA Division
            [
                'name' => 'Kartika PKA',
                'full_name' => 'Kartika Dewi',
                'email' => 'kartika.pka@etquizzes.com',
                'division' => 'PKA',
                'phone' => '081234567901',
                'address' => 'PKA Department, Floor 6'
            ],
            [
                'name' => 'Lukman PKA',
                'full_name' => 'Lukman Hakim',
                'email' => 'lukman.pka@etquizzes.com',
                'division' => 'PKA',
                'phone' => '081234567902',
                'address' => 'PKA Department, Floor 6'
            ],

            // RG Division
            [
                'name' => 'Maya RG',
                'full_name' => 'Maya Anggraini',
                'email' => 'maya.rg@etquizzes.com',
                'division' => 'RG',
                'phone' => '081234567903',
                'address' => 'RG Department, Floor 7'
            ],
            [
                'name' => 'Nanda RG',
                'full_name' => 'Nanda Pratama',
                'email' => 'nanda.rg@etquizzes.com',
                'division' => 'RG',
                'phone' => '081234567904',
                'address' => 'RG Department, Floor 7'
            ],

            // SAPRAS Division
            [
                'name' => 'Oktavia SAPRAS',
                'full_name' => 'Oktavia Wulandari',
                'email' => 'oktavia.sapras@etquizzes.com',
                'division' => 'SAPRAS',
                'phone' => '081234567905',
                'address' => 'SAPRAS Department, Floor 8'
            ],
            [
                'name' => 'Pramono SAPRAS',
                'full_name' => 'Pramono Jati',
                'email' => 'pramono.sapras@etquizzes.com',
                'division' => 'SAPRAS',
                'phone' => '081234567906',
                'address' => 'SAPRAS Department, Floor 8'
            ],
        ];

        // Loop through employees and create them
        foreach ($employees as $employeeData) {
            // Find division ID by name
            $division = Division::where('name', $employeeData['division'])->first();

            User::create([
                'name' => $employeeData['name'],
                'full_name' => $employeeData['full_name'],
                'email' => $employeeData['email'],
                'password' => Hash::make('password123'), // Default password untuk semua user
                'role' => 'employee',
                'status' => 'active',
                'division_id' => $division ? $division->id : null,
                'phone' => $employeeData['phone'],
                'address' => $employeeData['address'],
            ]);
        }
    }
}
