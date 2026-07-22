<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Seeder;

class DivisionEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            'HRD', 'STAFSUS', 'KPJ', 'PENDIDIKAN BOGOR', 'LPS',
            'PENDIDIKAN JAKARTA', 'MEDIA', 'RG', 'KEUANGAN', 'PKA',
            'SAPRAS OB', 'SAPRAS DRIVER',
        ];

        foreach ($divisions as $name) {
            Division::firstOrCreate(['name' => $name]);
        }

        $this->command->info('Divisions created successfully.');

        $assignments = [
            'HRD'              => ['Budi Santoso'],
            'STAFSUS'          => ['Thyeadi Tungson'],
            'KPJ'              => [
                'Moh. Napis Aropi', 'Irvan Sanjaya', 'Ryky Tunggal Saputra Aji',
                'Peggy Nurida Asri', 'Muhamad Ihsan',
            ],
            'PENDIDIKAN BOGOR' => [
                'Fikri Fauzi', 'Mia Nur Aprilia', 'M. Zidan Mahardika',
                'Febriyana', 'Devi Novi Aryanti',
            ],
            'LPS'              => [
                'Ardianto', 'Moehammad Buchori Nugraha', 'Fazar Zulham Ibrahim',
                'Agus Sutisna', 'Nanda Lindawati', 'Tri Jumsari',
            ],
            'PENDIDIKAN JAKARTA' => [
                'M Rijwan', 'Khomsalia Denmasti Harun', 'Desi Dwi Ariyanti',
                'Rangga Pradana', 'Cahya aditya', 'Ramadhan Setiawan',
                'Siti Khoerunnisa',
            ],
            'MEDIA'            => [
                'Siti Fatimah', 'Roy Yulio', 'Sang Baharsyah', 'Ali Zulfikar Ralibie',
            ],
            'RG'               => [
                'Muhamad Seis Kusuma Negara', 'Hendra Minar', 'Weni Wulan Sari',
                'Nabila Nurhakimah',
            ],
            'KEUANGAN'         => ['Desi Kurnia Wati', 'Dwi Atika Permata Sari'],
            'PKA'              => [
                'Ferdianto', 'Siti Maesaroh', 'Muchammad Fachri',
                'Dedi Wahyudi', 'Sutriyati', 'Vega Oktaviana',
                'Andri Imam Munandar', 'Siti Alpiyah',
            ],
            'SAPRAS OB'        => [
                'Lukmanul Hakim', 'Rully Dwiandika Yunus', 'Muhamad Rijal Apriansyah',
            ],
            'SAPRAS DRIVER'    => ['Ragil agustian alpiansyah', 'Deri Rahman'],
        ];

        $updated = 0;
        $notFound = [];

        foreach ($assignments as $divisionName => $names) {
            $division = Division::where('name', $divisionName)->first();

            foreach ($names as $name) {
                $user = User::where('role', 'employee')
                    ->where(function ($q) use ($name) {
                        $q->where('full_name', $name)
                          ->orWhere('name', $name);
                    })
                    ->first();

                if ($user) {
                    $user->update(['division_id' => $division->id]);
                    $updated++;
                } else {
                    $notFound[] = $name;
                }
            }
        }

        $this->command->info("Updated {$updated} employees with division.");

        if (!empty($notFound)) {
            $this->command->warn('Employees not found (check spelling):');
            foreach ($notFound as $name) {
                $this->command->warn("  - {$name}");
            }
        }
    }
}
