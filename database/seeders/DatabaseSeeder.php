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
        // Create default divisions
        $divisions = [
            ['name' => 'Human Resources', 'description' => 'Human Resources Department'],
            ['name' => 'Engineering', 'description' => 'Engineering Department'],
            ['name' => 'Marketing', 'description' => 'Marketing Department'],
            ['name' => 'Finance', 'description' => 'Finance Department'],
            ['name' => 'Sales', 'description' => 'Sales Department'],
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

        // Create HR account
        User::create([
            'name' => 'HR Admin',
            'full_name' => 'HR Administrator',
            'email' => 'hr@etquizzes.com',
            'password' => Hash::make('password123'),
            'role' => 'hr',
            'status' => 'active',
            'division_id' => 1,
        ]);

        // Create sample employee
        User::create([
            'name' => 'John Doe',
            'full_name' => 'John Doe',
            'email' => 'john@etquizzes.com',
            'password' => Hash::make('password123'),
            'role' => 'employee',
            'status' => 'active',
            'division_id' => 2,
        ]);

        // Create additional employees
        User::create([
            'name' => 'Jane Smith',
            'full_name' => 'Jane Smith',
            'email' => 'jane@etquizzes.com',
            'password' => Hash::make('password123'),
            'role' => 'employee',
            'status' => 'active',
            'division_id' => 3,
        ]);

        User::create([
            'name' => 'Bob Johnson',
            'full_name' => 'Bob Johnson',
            'email' => 'bob@etquizzes.com',
            'password' => Hash::make('password123'),
            'role' => 'employee',
            'status' => 'active',
            'division_id' => 4,
        ]);
    }
}
