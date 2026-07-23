<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun HR (jika belum ada)
        User::updateOrCreate(
            ['email' => 'hr@etquizzes.com'],
            [
                'name'              => 'HR Admin',
                'full_name'         => 'HR Administrator',
                'password'          => Hash::make('password123'),
                'role'              => 'hr',
                'status'            => 'active',
                'division_id'       => null,
                'phone'             => '081234567890',
                'address'           => 'HR Department',
                'avatar'            => null,
                'email_verified_at' => now(),
            ]
        );

        // Data karyawan (65 orang) sesuai daftar
        $employees = [
            ['kode' => '1042001', 'name' => 'Dr. R. Ridwan Hasan Saputra, M.Si.', 'email' => 'ridwan.hasan@gmail.com'],
            ['kode' => '2042001', 'name' => 'Anis Kurniasih', 'email' => 'anis.kurniasih@gmail.com'],
            ['kode' => '1012009', 'name' => 'Desi Kurnia Wati', 'email' => 'desi.wati@gmail.com'],
            ['kode' => '2072009', 'name' => 'Weni Wulan Sari', 'email' => 'weni.sari@gmail.com'],
            ['kode' => '1052010', 'name' => 'Siti Khoerunnisa', 'email' => 'siti.khoerunnisa@gmail.com'],
            ['kode' => '1102012', 'name' => 'Muchammad Fachri', 'email' => 'muchammad.fachri@gmail.com'],
            ['kode' => '1072013', 'name' => 'Sutriyati', 'email' => 'sutriyati@gmail.com'],
            ['kode' => '1072014', 'name' => 'Thyeadi Tungson', 'email' => 'thyeadi.tungson@gmail.com'],
            ['kode' => '1092014', 'name' => 'M Rijwan', 'email' => 'm.rijwan@gmail.com'],
            ['kode' => '2112014', 'name' => 'Tri Jumsari', 'email' => 'tri.jumsari@gmail.com'],
            ['kode' => '1092015', 'name' => 'Muhammad Ihsan', 'email' => 'muhammad.ihsan@gmail.com'],
            ['kode' => '1062016', 'name' => 'Ryky Tunggal Saputra Aji', 'email' => 'ryky.aji@gmail.com'],
            ['kode' => '1012017', 'name' => 'Muhamad Seis Kusumanegara', 'email' => 'muhamad.kusumanegara@gmail.com'],
            ['kode' => '2012017', 'name' => 'Nabila Nurhakimah', 'email' => 'nabila.nurhakimah@gmail.com'],
            ['kode' => '1012018', 'name' => 'Rheta Rezkianti', 'email' => 'rheta.rezkianti@gmail.com'],
            ['kode' => '1012019', 'name' => 'Lukmanul Hakim', 'email' => 'lukmanul.hakim@gmail.com'],
            ['kode' => '2012019', 'name' => 'Dwi Atika Permata Sari', 'email' => 'dwi.sari@gmail.com'],
            ['kode' => '1102019', 'name' => 'Ali Zulfikar', 'email' => 'ali.zulfikar@gmail.com'],
            ['kode' => '1122019', 'name' => 'Andri Imam Munandar', 'email' => 'andri.munandar@gmail.com'],
            ['kode' => '2032021', 'name' => 'Vega Oktaviana', 'email' => 'vega.oktaviana@gmail.com'],
            ['kode' => '3032021', 'name' => 'Ardianto', 'email' => 'ardianto@gmail.com'],
            ['kode' => '2092021', 'name' => 'Dedi Wahyudi', 'email' => 'dedi.wahyudi@gmail.com'],
            ['kode' => '1102021', 'name' => 'Hendra Minar', 'email' => 'hendra.minar@gmail.com'],
            ['kode' => '3102021', 'name' => 'Nanda Lindawati', 'email' => 'nanda.lindawati@gmail.com'],
            ['kode' => '4102021', 'name' => 'Agus Sutisna', 'email' => 'agus.sutisna@gmail.com'],
            ['kode' => '5102021', 'name' => 'Fikri Fauzi', 'email' => 'fikri.fauzi@gmail.com'],
            ['kode' => '6122021', 'name' => 'Devi Ariyanti', 'email' => 'devi.ariyanti@gmail.com'],
            ['kode' => '3122021', 'name' => 'Muhamad Rijal Apriansyah', 'email' => 'muhamad.apriansyah@gmail.com'],
            ['kode' => '2022022', 'name' => 'Siti Fatimah', 'email' => 'siti.fatimah@gmail.com'],
            ['kode' => '2032022', 'name' => 'Roy Yulio', 'email' => 'roy.yulio@gmail.com'],
            ['kode' => '1042022', 'name' => 'Siti Maesaroh', 'email' => 'siti.maesaroh@gmail.com'],
            ['kode' => '3042022', 'name' => 'Febriyana', 'email' => 'febriyana@gmail.com'],
            ['kode' => '4042022', 'name' => 'Moh. Napis Aropi', 'email' => 'moh.aropi@gmail.com'],
            ['kode' => '3052022', 'name' => 'Rully Dwiandika Yunus', 'email' => 'rully.yunus@gmail.com'],
            ['kode' => '1082022', 'name' => 'Mia Nur Aprilia', 'email' => 'mia.aprilia@gmail.com'],
            ['kode' => '2082022', 'name' => 'Siti Alpiah', 'email' => 'siti.alpiah@gmail.com'],
            ['kode' => '2092022', 'name' => 'Desi Dwi Ariyanti', 'email' => 'desi.ariyanti@gmail.com'],
            ['kode' => '3092022', 'name' => 'Ragil Agustian Alpiansyah', 'email' => 'ragil.alpiansyah@gmail.com'],
            ['kode' => '1032023', 'name' => 'Fajar Zulham Ibrahim', 'email' => 'fajar.ibrahim@gmail.com'],
            ['kode' => '1102023', 'name' => 'M Zidan Mahardika', 'email' => 'm.zidan@gmail.com'],
            ['kode' => '4112023', 'name' => 'Moehammad Buchori', 'email' => 'moehammad.buchori@gmail.com'],
            ['kode' => '5112023', 'name' => 'Cahya Aditya', 'email' => 'cahya.aditya@gmail.com'],
            ['kode' => '1032024', 'name' => 'Rangga Pradana', 'email' => 'rangga.pradana@gmail.com'],
            ['kode' => '1072024', 'name' => 'Sang Baharsyah', 'email' => 'sang.baharsyah@gmail.com'],
            ['kode' => '1092024', 'name' => 'Khomsalia Denmasti Harun', 'email' => 'khomsalia.harun@gmail.com'],
            ['kode' => '2092024', 'name' => 'Ramadhan Setiawan', 'email' => 'ramadhan.setiawan@gmail.com'],
            ['kode' => '1022025', 'name' => 'Adinda Baby Cantika Dewi', 'email' => 'adinda.dewi@gmail.com'],
            ['kode' => '2012025', 'name' => 'Irvan Sanjaya', 'email' => 'irvan.sanjaya@gmail.com'],
            ['kode' => '1072025', 'name' => 'Deri Rahman', 'email' => 'deri.rahman@gmail.com'],
            ['kode' => '1082025', 'name' => 'Isna Nur Fajriah', 'email' => 'isna.fajriah@gmail.com'],
            ['kode' => '2082025', 'name' => 'Arisna Dwi Hapsari', 'email' => 'arisna.hapsari@gmail.com'],
            ['kode' => '3082025', 'name' => 'Suhardi Prayitno', 'email' => 'suhardi.prayitno@gmail.com'],
            ['kode' => '4082025', 'name' => 'Muhammad Rafiq Alfiansyah', 'email' => 'muhammad.alfiansyah@gmail.com'],
            ['kode' => '5082025', 'name' => 'Ayu Mandasari', 'email' => 'ayu.mandasari@gmail.com'],
            ['kode' => '6082025', 'name' => 'Selfia Annatasya', 'email' => 'selfia.annatasya@gmail.com'],
            ['kode' => '1092025', 'name' => 'Ferdianto', 'email' => 'ferdianto@gmail.com'],
            ['kode' => '2092025', 'name' => 'Siti Silvia Handayani', 'email' => 'siti.handayani@gmail.com'],
            ['kode' => '1112025', 'name' => 'Ziban Lesmana Sutiawan', 'email' => 'ziban.sutiawan@gmail.com'],
            ['kode' => '2112025', 'name' => 'Rathri Candra Dewi', 'email' => 'rathri.dewi@gmail.com'],
            ['kode' => '1012026', 'name' => 'M. Ariek Hidayat', 'email' => 'm.ariekhidayat@gmail.com'],
            ['kode' => '1032026', 'name' => 'M Haikal Catur Saputra', 'email' => 'm.haikal@gmail.com'],
            ['kode' => '2032026', 'name' => 'Peggy Nurida Asri', 'email' => 'peggy.asri@gmail.com'],
            ['kode' => '1062026', 'name' => 'Jovita Anggraeni', 'email' => 'jovita.anggraeni@gmail.com'],
            ['kode' => '2062026', 'name' => 'Muhammad Fakhrizal Garnindyo', 'email' => 'muhammad.garnindyo@gmail.com'],
            ['kode' => '3062026', 'name' => 'Muhammad Burhanudin', 'email' => 'muhammad.burhanudin@gmail.com'],
        ];

        foreach ($employees as $emp) {
            User::updateOrCreate(
                ['email' => $emp['email']],
                [
                    'name'              => $emp['name'],
                    'full_name'         => $emp['name'],
                    'password'          => Hash::make('password123'),
                    'role'              => 'employee',
                    'status'            => 'active',
                    'division_id'       => null,
                    'phone'             => null,
                    'address'           => null,
                    'avatar'            => null,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
