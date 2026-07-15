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

        // Create employees
        // Catatan: divisi tidak disebutkan untuk data ini, jadi division_id diset null.
        // Email dibuat dari nama depan + nama belakang (gelar/inisial huruf tunggal diabaikan).
        $employees = [
            ['name' => 'Dr. R. Ridwan Hasan Saputra, M.Si.', 'email' => 'ridwan.saputra@etquizzes.com', 'password' => 'password123'],
            ['name' => 'Anis Kurniasih', 'email' => 'anis.kurniasih@etquizzes.com', 'password' => 'password124'],
            ['name' => 'Desi Kurnia Wati', 'email' => 'desi.wati@etquizzes.com', 'password' => 'password125'],
            ['name' => 'Weni Wulan Sari', 'email' => 'weni.sari@etquizzes.com', 'password' => 'password126'],
            ['name' => 'Siti Khoerunnisa', 'email' => 'siti.khoerunnisa@etquizzes.com', 'password' => 'password127'],
            ['name' => 'Muchammad Fachri', 'email' => 'muchammad.fachri@etquizzes.com', 'password' => 'password128'],
            ['name' => 'Sutriyati', 'email' => 'sutriyati@etquizzes.com', 'password' => 'password129'],
            ['name' => 'Thyeadi Tungson', 'email' => 'thyeadi.tungson@etquizzes.com', 'password' => 'password130'],
            ['name' => 'M Rijwan', 'email' => 'm.rijwan@etquizzes.com', 'password' => 'password131'],
            ['name' => 'Tri Jumsari', 'email' => 'tri.jumsari@etquizzes.com', 'password' => 'password132'],
            ['name' => 'Muhammad Ihsan', 'email' => 'muhammad.ihsan@etquizzes.com', 'password' => 'password133'],
            ['name' => 'Ryky Tunggal Saputra Aji', 'email' => 'ryky.aji@etquizzes.com', 'password' => 'password134'],
            ['name' => 'Muhamad Seis Kusumanegara', 'email' => 'muhamad.kusumanegara@etquizzes.com', 'password' => 'password135'],
            ['name' => 'Nabila Nurhakimah', 'email' => 'nabila.nurhakimah@etquizzes.com', 'password' => 'password136'],
            ['name' => 'Rheta Rezkianti', 'email' => 'rheta.rezkianti@etquizzes.com', 'password' => 'password137'],
            ['name' => 'Lukmanul Hakim', 'email' => 'lukmanul.hakim@etquizzes.com', 'password' => 'password138'],
            ['name' => 'Dwi Atika Permata Sari', 'email' => 'dwi.sari@etquizzes.com', 'password' => 'password139'],
            ['name' => 'Ali Zulfikar', 'email' => 'ali.zulfikar@etquizzes.com', 'password' => 'password140'],
            ['name' => 'Andri Imam Munandar', 'email' => 'andri.munandar@etquizzes.com', 'password' => 'password141'],
            ['name' => 'Vega Oktaviana', 'email' => 'vega.oktaviana@etquizzes.com', 'password' => 'password142'],
            ['name' => 'Ardianto', 'email' => 'ardianto@etquizzes.com', 'password' => 'password143'],
            ['name' => 'Dedi Wahyudi', 'email' => 'dedi.wahyudi@etquizzes.com', 'password' => 'password144'],
            ['name' => 'Hendra Minar', 'email' => 'hendra.minar@etquizzes.com', 'password' => 'password145'],
            ['name' => 'Nanda Lindawati', 'email' => 'nanda.lindawati@etquizzes.com', 'password' => 'password146'],
            ['name' => 'Agus Sutisna', 'email' => 'agus.sutisna@etquizzes.com', 'password' => 'password147'],
            ['name' => 'Fikri Fauzi', 'email' => 'fikri.fauzi@etquizzes.com', 'password' => 'password148'],
            ['name' => 'Devi Ariyanti', 'email' => 'devi.ariyanti@etquizzes.com', 'password' => 'password149'],
            ['name' => 'Muhamad Rijal Apriansyah', 'email' => 'muhamad.apriansyah@etquizzes.com', 'password' => 'password150'],
            ['name' => 'Siti Fatimah', 'email' => 'siti.fatimah@etquizzes.com', 'password' => 'password151'],
            ['name' => 'Roy Yulio', 'email' => 'roy.yulio@etquizzes.com', 'password' => 'password152'],
            ['name' => 'Siti Maesaroh', 'email' => 'siti.maesaroh@etquizzes.com', 'password' => 'password153'],
            ['name' => 'Febriyana', 'email' => 'febriyana@etquizzes.com', 'password' => 'password154'],
            ['name' => 'Moh. Napis Aropi', 'email' => 'moh.aropi@etquizzes.com', 'password' => 'password155'],
            ['name' => 'Rully Dwiandika Yunus', 'email' => 'rully.yunus@etquizzes.com', 'password' => 'password156'],
            ['name' => 'Mia Nur Aprilia', 'email' => 'mia.aprilia@etquizzes.com', 'password' => 'password157'],
            ['name' => 'Siti Alpiah', 'email' => 'siti.alpiah@etquizzes.com', 'password' => 'password158'],
            ['name' => 'Desi Dwi Ariyanti', 'email' => 'desi.ariyanti@etquizzes.com', 'password' => 'password159'],
            ['name' => 'Ragil Agustian Alpiansyah', 'email' => 'ragil.alpiansyah@etquizzes.com', 'password' => 'password160'],
            ['name' => 'Fajar Zulham Ibrahim', 'email' => 'fajar.ibrahim@etquizzes.com', 'password' => 'password161'],
            ['name' => 'M Zidan Mahardika', 'email' => 'm.mahardika@etquizzes.com', 'password' => 'password162'],
            ['name' => 'Moehammad Buchori', 'email' => 'moehammad.buchori@etquizzes.com', 'password' => 'password163'],
            ['name' => 'Cahya Aditya', 'email' => 'cahya.aditya@etquizzes.com', 'password' => 'password164'],
            ['name' => 'Rangga Pradana', 'email' => 'rangga.pradana@etquizzes.com', 'password' => 'password165'],
            ['name' => 'Sang Baharsyah', 'email' => 'sang.baharsyah@etquizzes.com', 'password' => 'password166'],
            ['name' => 'Khomsalia Denmasti Harun', 'email' => 'khomsalia.harun@etquizzes.com', 'password' => 'password167'],
            ['name' => 'Ramadhan Setiawan', 'email' => 'ramadhan.setiawan@etquizzes.com', 'password' => 'password168'],
            ['name' => 'Adinda Baby Cantika Dewi', 'email' => 'adinda.dewi@etquizzes.com', 'password' => 'password169'],
            ['name' => 'Irvan Sanjaya', 'email' => 'irvan.sanjaya@etquizzes.com', 'password' => 'password170'],
            ['name' => 'Deri Rahman', 'email' => 'deri.rahman@etquizzes.com', 'password' => 'password171'],
            ['name' => 'Isna Nur Fajriah', 'email' => 'isna.fajriah@etquizzes.com', 'password' => 'password172'],
            ['name' => 'Arisna Dwi Hapsari', 'email' => 'arisna.hapsari@etquizzes.com', 'password' => 'password173'],
            ['name' => 'Suhardi Prayitno', 'email' => 'suhardi.prayitno@etquizzes.com', 'password' => 'password174'],
            ['name' => 'Muhammad Rafiq Alfiansyah', 'email' => 'muhammad.alfiansyah@etquizzes.com', 'password' => 'password175'],
            ['name' => 'Ayu Mandasari', 'email' => 'ayu.mandasari@etquizzes.com', 'password' => 'password176'],
            ['name' => 'Selfia Annatasya', 'email' => 'selfia.annatasya@etquizzes.com', 'password' => 'password177'],
            ['name' => 'Ferdianto', 'email' => 'ferdianto@etquizzes.com', 'password' => 'password178'],
            ['name' => 'Siti Silvia Handayani', 'email' => 'siti.handayani@etquizzes.com', 'password' => 'password179'],
            ['name' => 'Ziban Lesmana Sutiawan', 'email' => 'ziban.sutiawan@etquizzes.com', 'password' => 'password180'],
            ['name' => 'Rathri Candra Dewi', 'email' => 'rathri.dewi@etquizzes.com', 'password' => 'password181'],
            ['name' => 'M. Ariek Hidayat', 'email' => 'm.hidayat@etquizzes.com', 'password' => 'password182'],
            ['name' => 'M Haikal Catur Saputra', 'email' => 'm.saputra@etquizzes.com', 'password' => 'password183'],
            ['name' => 'Peggy Nurida Asri', 'email' => 'peggy.asri@etquizzes.com', 'password' => 'password184'],
            ['name' => 'Jovita Anggraeni', 'email' => 'jovita.anggraeni@etquizzes.com', 'password' => 'password185'],
            ['name' => 'Muhammad Fakhrizal Garnindyo', 'email' => 'muhammad.garnindyo@etquizzes.com', 'password' => 'password186'],
            ['name' => 'Muhammad Burhanudin', 'email' => 'muhammad.burhanudin@etquizzes.com', 'password' => 'password187'],
        ];

        // Loop through employees and create them
        foreach ($employees as $employeeData) {
            User::create([
                'name' => $employeeData['name'],
                'full_name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => Hash::make($employeeData['password']),
                'role' => 'employee',
                'status' => 'active',
                'division_id' => null, // divisi tidak disebutkan, silakan sesuaikan jika perlu
                'phone' => null,
                'address' => null,
            ]);
        }
    }
}
