<?php

namespace Database\Seeders;

use App\Models\Criterion;
use Illuminate\Database\Seeder;

class CriterionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            [
                'code' => 'C1',
                'name' => 'Harga',
                'type' => 'cost',
                'unit' => 'Rp',
                'description' => 'Harga beli laptop dalam Rupiah (kriteria biaya: semakin rendah harga semakin diprioritaskan).',
            ],
            [
                'code' => 'C2',
                'name' => 'Performa Prosesor',
                'type' => 'benefit',
                'unit' => 'skor 1-10',
                'description' => 'Estimasi performa CPU/prosesor untuk beban komputasi harian hingga berat pada skala 1-10.',
            ],
            [
                'code' => 'C3',
                'name' => 'Kapasitas RAM',
                'type' => 'benefit',
                'unit' => 'GB',
                'description' => 'Kapasitas memori utama (RAM) dalam Gigabyte (GB) untuk kelancaran multitasking.',
            ],
            [
                'code' => 'C4',
                'name' => 'Kapasitas Penyimpanan',
                'type' => 'benefit',
                'unit' => 'GB',
                'description' => 'Kapasitas media penyimpanan SSD internal dalam Gigabyte (GB).',
            ],
            [
                'code' => 'C5',
                'name' => 'Bobot Laptop',
                'type' => 'cost',
                'unit' => 'kg',
                'description' => 'Berat fisik unit laptop dalam kilogram (kriteria biaya: semakin ringan semakin portabel untuk mahasiswa).',
            ],
            [
                'code' => 'C6',
                'name' => 'Daya Tahan Baterai',
                'type' => 'benefit',
                'unit' => 'jam',
                'description' => 'Perkiraan durasi ketahanan baterai untuk komputasi harian mahasiswa tanpa charger.',
            ],
        ];

        foreach ($criteria as $item) {
            Criterion::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
