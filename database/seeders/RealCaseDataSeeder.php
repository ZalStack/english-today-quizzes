<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\User;
use App\Models\VideoChallenge;
use Illuminate\Database\Seeder;

class RealCaseDataSeeder extends Seeder
{
    public function run(): void
    {
        $hr = User::where('role', 'hr')->first();
        if (!$hr) {
            $hr = User::firstOrCreate(
                ['email' => 'admin@englishtoday.co.id'],
                [
                    'name' => 'HR Administrator',
                    'full_name' => 'HR Administrator',
                    'password' => bcrypt('password123'),
                    'role' => 'hr',
                    'status' => 'active',
                ]
            );
        }

        // 1. Kategori Kuis
        $category = QuizCategory::firstOrCreate(
            ['name' => 'English Daily Communication'],
            ['description' => 'Kategori kuis komunikasi bahasa Inggris sehari-hari di tempat kerja.']
        );

        // 2. Kuis Aktif Real Case
        $quiz = Quiz::updateOrCreate(
            ['title' => 'Daily English Workplace & Vocabulary Challenge'],
            [
                'category_id' => $category->id,
                'created_by' => $hr->id,
                'description' => 'Uji pemahaman kosa kata dan tata bahasa Inggris kerja harianmu untuk mengumpulkan poin dan membuka 3D Pet Sanctuary!',
                'duration' => 15,
                'status' => 'active',
            ]
        );

        $questionsData = [
            [
                'question_text' => 'Which sentence is the most polite way to ask for assistance during a team meeting?',
                'question_type' => 'multiple_choice',
                'options' => json_encode([
                    'Give me a hand right now.',
                    'Could you please help me with this project report?',
                    'I need your help, hurry up.',
                    'You must assist me today.',
                ]),
                'correct_answer' => 'Could you please help me with this project report?',
                'points' => 20,
                'order_number' => 1,
            ],
            [
                'question_text' => 'The client requested that we send the revised proposal by 5:00 PM today.',
                'question_type' => 'true_false',
                'options' => json_encode(['True', 'False']),
                'correct_answer' => 'True',
                'points' => 20,
                'order_number' => 2,
            ],
            [
                'question_text' => 'What is the opposite meaning (antonym) of the word "CONFIDENTIAL" in a business setting?',
                'question_type' => 'multiple_choice',
                'options' => json_encode([
                    'Public',
                    'Secret',
                    'Private',
                    'Classified',
                ]),
                'correct_answer' => 'Public',
                'points' => 20,
                'order_number' => 3,
            ],
            [
                'question_text' => 'Complete the sentence: "We look forward to ______ (meet / meeting) you next week."',
                'question_type' => 'short_answer',
                'options' => null,
                'correct_answer' => 'meeting',
                'points' => 20,
                'order_number' => 4,
            ],
            [
                'question_text' => 'Briefly describe in English how you introduce yourself to a new colleague on your first day at work.',
                'question_type' => 'essay',
                'options' => null,
                'correct_answer' => 'Hello, my name is... and I am glad to join the team.',
                'points' => 20,
                'order_number' => 5,
            ],
        ];

        foreach ($questionsData as $q) {
            Question::updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'order_number' => $q['order_number'],
                ],
                $q
            );
        }

        // 3. Video Challenge Aktif Real Case
        VideoChallenge::updateOrCreate(
            ['title' => 'Self-Introduction & Work Passion Speaking Challenge'],
            [
                'description' => 'Rekam video speaking berdurasi 1-2 menit dalam bahasa Inggris menceritakan perkenalan diri, peranmu di divisi, dan apa yang membuatmu bersemangat dalam bekerja. Unggah ke YouTube (Unlisted) atau Google Drive, lalu kumpulkan link-nya di sini!',
                'created_by' => $hr->id,
                'is_active' => true,
                'material_title' => 'Panduan & Tips Speaking Video Challenge',
                'material_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'kisi_kisi_title' => 'Rubrik Penilaian & Contoh Script Opening',
                'kisi_kisi_link' => 'https://drive.google.com/file/d/example-rubric/view',
            ]
        );
    }
}
