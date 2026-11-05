<?php

namespace Database\Seeders;

use App\Models\Alternative;
use App\Models\AlternativeValue;
use App\Models\Criterion;
use Illuminate\Database\Seeder;

class AlternativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * PERHATIAN & CATATAN PENTING:
     * Seluruh data spesifikasi laptop, harga, dan skor prosesor di bawah ini merupakan data terverifikasi
     * yang mencakup lini laptop mahasiswa harian, ultrabook, dan lini laptop gaming di pasar Indonesia
     * untuk keperluan sistem SPK (AHP-TOPSIS) mata kuliah Manajemen Sains & Rekayasa Web.
     */
    public function run(): void
    {
        $criteria = Criterion::all()->keyBy('code');

        if ($criteria->count() < 6) {
            $this->command->error('Kriteria C1-C6 belum lengkap di database. Jalankan CriterionSeeder terlebih dahulu.');

            return;
        }

        $laptops = [
            // ── LINI LAPTOP MAHASISWA & HARIAN ──────────────────────────────
            [
                'name' => 'Acer Aspire 3 A314-23M',
                'brand' => 'Acer',
                'price' => 6299000,
                'image' => 'images/laptops/acer-aspire-3.jpg',
                'description' => 'Laptop entry-level ekonomis untuk pengetikan dokumen, kuliah daring, dan komputasi harian mahasiswa dengan prosesor AMD Ryzen 3 7320U.',
                'values' => [
                    'C1' => 6299000, // Harga (Rp)
                    'C2' => 5.5,     // Performa CPU (skor 1-10)
                    'C3' => 8,       // RAM (GB)
                    'C4' => 512,     // Storage (GB)
                    'C5' => 1.40,    // Bobot (kg)
                    'C6' => 7.0,     // Baterai (jam)
                ],
            ],
            [
                'name' => 'Lenovo IdeaPad Slim 3 14IAH8',
                'brand' => 'Lenovo',
                'price' => 7999000,
                'image' => 'images/laptops/lenovo-ideapad-slim-3.jpg',
                'description' => 'Pilihan mahasiswa sains dan teknik dengan prosesor Intel Core i5-12450H seri bertenaga tinggi untuk komputasi cepat.',
                'values' => [
                    'C1' => 7999000,
                    'C2' => 7.2,
                    'C3' => 8,
                    'C4' => 512,
                    'C5' => 1.43,
                    'C6' => 6.5,
                ],
            ],
            [
                'name' => 'ASUS Vivobook Go 14 E1404FA',
                'brand' => 'ASUS',
                'price' => 7299000,
                'image' => 'images/laptops/asus-vivobook-14.jpg',
                'description' => 'Ultrabook ringkas berdurabilitas militer MIL-STD-810H dengan layar lay-flat 180 derajat dan AMD Ryzen 5 7520U RAM 16GB.',
                'values' => [
                    'C1' => 7299000,
                    'C2' => 6.5,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 1.38,
                    'C6' => 7.5,
                ],
            ],
            [
                'name' => 'HP 14s-dq5115TU',
                'brand' => 'HP',
                'price' => 8499000,
                'image' => 'images/laptops/hp-14s.jpg',
                'description' => 'Laptop kuliah harian berdesain bersih dengan bezel tipis micro-edge display dan Intel Core i5-1235U hemat daya.',
                'values' => [
                    'C1' => 8499000,
                    'C2' => 6.8,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 1.46,
                    'C6' => 7.0,
                ],
            ],
            [
                'name' => 'Lenovo IdeaPad Slim 5 14IRL8',
                'brand' => 'Lenovo',
                'price' => 11499000,
                'image' => 'images/laptops/lenovo-yoga-slim-7.jpg',
                'description' => 'Laptop bodi aluminium kokoh dengan layar 100% sRGB, kamera FHD IR, dan Intel Core i5-13500H untuk coding dan multimedia.',
                'values' => [
                    'C1' => 11499000,
                    'C2' => 8.0,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 1.46,
                    'C6' => 8.0,
                ],
            ],
            [
                'name' => 'ASUS Vivobook 14X K3405VC',
                'brand' => 'ASUS',
                'price' => 12999000,
                'image' => 'images/laptops/msi-modern-14.jpg',
                'description' => 'Laptop serbaguna untuk mahasiswa desain grafis & multimedia dengan GPU diskrit NVIDIA GeForce RTX 3050 dan Intel Core i5-13500H.',
                'values' => [
                    'C1' => 12999000,
                    'C2' => 8.4,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 1.60,
                    'C6' => 5.5,
                ],
            ],
            [
                'name' => 'Acer Swift Go 14 OLED SFG14-71',
                'brand' => 'Acer',
                'price' => 13499000,
                'image' => 'images/laptops/acer-swift-go-14.jpg',
                'description' => 'Laptop ultra-portabel bersertifikasi Intel Evo dengan layar tajam OLED 2.8K 90Hz dan Intel Core i7-13700H serta SSD 1TB.',
                'values' => [
                    'C1' => 13499000,
                    'C2' => 8.8,
                    'C3' => 16,
                    'C4' => 1024,
                    'C5' => 1.25,
                    'C6' => 8.5,
                ],
            ],
            [
                'name' => 'Apple MacBook Air 13 M2',
                'brand' => 'Apple',
                'price' => 14999000,
                'image' => 'images/laptops/macbook-air-m2.jpg',
                'description' => 'Desain unibody aluminium ultra-tipis tanpa kipas (silent operation), efisiensi chip Apple M2, dan baterai hingga 15 jam.',
                'values' => [
                    'C1' => 14999000,
                    'C2' => 8.5,
                    'C3' => 8,
                    'C4' => 256,
                    'C5' => 1.24,
                    'C6' => 15.0,
                ],
            ],
            [
                'name' => 'ASUS Zenbook 14 OLED UX3405MA',
                'brand' => 'ASUS',
                'price' => 17299000,
                'image' => 'images/laptops/asus-zenbook-14.jpg',
                'description' => 'Ultrabook flagship dengan arsitektur Intel Core Ultra 7 155H berakselerator AI (NPU), RAM 32GB, dan bobot super ringan 1.2 kg.',
                'values' => [
                    'C1' => 17299000,
                    'C2' => 9.0,
                    'C3' => 32,
                    'C4' => 1024,
                    'C5' => 1.20,
                    'C6' => 11.0,
                ],
            ],
            [
                'name' => 'Apple MacBook Air 13 M3',
                'brand' => 'Apple',
                'price' => 18999000,
                'image' => 'images/laptops/macbook-air-m2.jpg',
                'description' => 'Generasi mutakhir chip Apple M3 3nm dengan dukungan dual external display, Wi-Fi 6E, dan efisiensi daya terbaik.',
                'values' => [
                    'C1' => 18999000,
                    'C2' => 9.2,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 1.24,
                    'C6' => 16.0,
                ],
            ],
            [
                'name' => 'Lenovo Legion Slim 5 16APH8',
                'brand' => 'Lenovo',
                'price' => 19999000,
                'image' => 'images/laptops/dell-inspiron-14.jpg',
                'description' => 'Mesin komputasi berat untuk riset kecerdasan buatan (AI), simulasi 3D, dan gaming dengan Ryzen 7 7840HS & NVIDIA RTX 4060.',
                'values' => [
                    'C1' => 19999000,
                    'C2' => 9.5,
                    'C3' => 16,
                    'C4' => 1024,
                    'C5' => 2.30,
                    'C6' => 5.0,
                ],
            ],
            [
                'name' => 'Axioo Hype 5 AMD X3',
                'brand' => 'Axioo',
                'price' => 5499000,
                'image' => 'images/laptops/axioo-hype-5.jpg',
                'description' => 'Pilihan laptop buatan dalam negeri berharga terjangkau dengan prosesor AMD Ryzen 5 3500U yang fleksibel untuk di-upgrade.',
                'values' => [
                    'C1' => 5499000,
                    'C2' => 5.0,
                    'C3' => 8,
                    'C4' => 256,
                    'C5' => 1.42,
                    'C6' => 6.0,
                ],
            ],

            // ── LINI LAPTOP GAMING ──────────────────────────────────────────
            [
                'name' => 'ASUS TUF Gaming A15 FA506IC (2021)',
                'brand' => 'ASUS',
                'price' => 10999000,
                'image' => 'images/laptops/asus-vivobook-14.jpg',
                'description' => 'Laptop gaming entry-level tangguh berstandar militer MIL-STD-810H dengan AMD Ryzen 7 4800H 8-core dan NVIDIA GeForce RTX 3050 4GB untuk gaming kompetitif serta rendering 3D mahasiswa teknik.',
                'values' => [
                    'C1' => 10999000,
                    'C2' => 7.8,
                    'C3' => 8,
                    'C4' => 512,
                    'C5' => 2.30,
                    'C6' => 5.0,
                ],
            ],
            [
                'name' => 'Lenovo IdeaPad Gaming 3 15ACH6 (2022)',
                'brand' => 'Lenovo',
                'price' => 10499000,
                'image' => 'images/laptops/lenovo-ideapad-slim-3.jpg',
                'description' => 'Laptop gaming budget favorit mahasiswa dengan keyboard legendaris Lenovo ber-travel distance 1.5mm, sistem pendingin quad-vent, dan AMD Ryzen 5 5600H serta NVIDIA RTX 3050.',
                'values' => [
                    'C1' => 10499000,
                    'C2' => 7.6,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 2.25,
                    'C6' => 5.5,
                ],
            ],
            [
                'name' => 'MSI GF63 Thin 11UC (2022)',
                'brand' => 'MSI',
                'price' => 9799000,
                'image' => 'images/laptops/msi-modern-14.jpg',
                'description' => 'Laptop gaming ultra-ramping dan ringan (hanya 1.86 kg) berlapis bodi brushed aluminum, memadukan Intel Core i5-11400H dan RTX 3050 dengan portabilitas kuliah tinggi.',
                'values' => [
                    'C1' => 9799000,
                    'C2' => 7.4,
                    'C3' => 8,
                    'C4' => 512,
                    'C5' => 1.86,
                    'C6' => 4.5,
                ],
            ],
            [
                'name' => 'Acer Nitro 5 AN515-58 (2022/2023)',
                'brand' => 'Acer',
                'price' => 12499000,
                'image' => 'images/laptops/acer-aspire-3.jpg',
                'description' => 'Lini laptop gaming legendaris dengan dual-fan cooling CoolBoost, layar IPS 144Hz, dan prosesor 12-core Intel Core i5-12500H berpadu NVIDIA GeForce RTX 3050.',
                'values' => [
                    'C1' => 12499000,
                    'C2' => 8.2,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 2.20,
                    'C6' => 5.0,
                ],
            ],
            [
                'name' => 'HP Victus 15-fa0011TX (2023)',
                'brand' => 'HP',
                'price' => 11999000,
                'image' => 'images/laptops/hp-14s.jpg',
                'description' => 'Desain minimalis modern khas lini Victus tanpa estetika agresif, cocok untuk dibawa kuliah maupun bermain game dengan Intel Core i5-12450H dan audio Bang & Olufsen.',
                'values' => [
                    'C1' => 11999000,
                    'C2' => 7.9,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 2.29,
                    'C6' => 5.5,
                ],
            ],
            [
                'name' => 'Lenovo LOQ 15IRH8 (2023/2024)',
                'brand' => 'Lenovo',
                'price' => 14299000,
                'image' => 'images/laptops/lenovo-ideapad-slim-3.jpg',
                'description' => 'Sub-brand gaming terbaru Lenovo dengan teknologi pendingin turunan Legion, prosesor Intel Core i5-13420H, dan GPU arsitektur Ada Lovelace RTX 4050 6GB GDDR6 berfitur MUX Switch.',
                'values' => [
                    'C1' => 14299000,
                    'C2' => 8.4,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 2.40,
                    'C6' => 5.0,
                ],
            ],
            [
                'name' => 'ASUS TUF Gaming F15 FX507VU (2024)',
                'brand' => 'ASUS',
                'price' => 16499000,
                'image' => 'images/laptops/asus-vivobook-14.jpg',
                'description' => 'Laptop gaming militer berdaya tahan baterai 90Wh terbaik di kelasnya, layar 100% sRGB 144Hz G-Sync, dan Intel Core i7-13620H dengan RTX 4050 6GB TGP 140W untuk coding berat serta 3D render.',
                'values' => [
                    'C1' => 16499000,
                    'C2' => 8.9,
                    'C3' => 16,
                    'C4' => 1024,
                    'C5' => 2.20,
                    'C6' => 6.0,
                ],
            ],
            [
                'name' => 'MSI Cyborg 15 A12VE (2024)',
                'brand' => 'MSI',
                'price' => 12899000,
                'image' => 'images/laptops/msi-modern-14.jpg',
                'description' => 'Inovasi sasis tembus pandang cyberpunk futuristik dengan bobot di bawah 2 kg, mengusung arsitektur GPU RTX 4050 terbaru dan port I/O lengkap untuk multitasking mahasiswa.',
                'values' => [
                    'C1' => 12899000,
                    'C2' => 8.1,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 1.98,
                    'C6' => 4.5,
                ],
            ],
            [
                'name' => 'Axioo Pongo 760 V2 (2024)',
                'brand' => 'Axioo',
                'price' => 15799000,
                'image' => 'images/laptops/axioo-hype-5.jpg',
                'description' => 'Laptop gaming lokal Indonesia dengan rasio performa-harga terbaik di kelasnya, menghadirkan GPU monster NVIDIA RTX 4060 8GB VRAM (TGP 105W) dan prosesor Intel Core i7-13620H.',
                'values' => [
                    'C1' => 15799000,
                    'C2' => 9.0,
                    'C3' => 16,
                    'C4' => 512,
                    'C5' => 2.05,
                    'C6' => 4.5,
                ],
            ],
            [
                'name' => 'Acer Predator Helios Neo 16 PHN16-71 (2024)',
                'brand' => 'Acer',
                'price' => 21999000,
                'image' => 'images/laptops/acer-swift-go-14.jpg',
                'description' => 'Laptop gaming hardcore kasta atas dengan prosesor kelas desktop HX-series Intel Core i7-13700HX, pendingin kipas logam 3D AeroBlade Gen 5, liquid metal thermal, dan layar WQXGA 165Hz.',
                'values' => [
                    'C1' => 21999000,
                    'C2' => 9.4,
                    'C3' => 16,
                    'C4' => 1024,
                    'C5' => 2.60,
                    'C6' => 5.0,
                ],
            ],
            [
                'name' => 'ASUS ROG Zephyrus G14 GA403UV (2025)',
                'brand' => 'ASUS',
                'price' => 26499000,
                'image' => 'images/laptops/asus-zenbook-14.jpg',
                'description' => 'Ultrabook gaming premium dengan bodi CNC-machined aluminium, layar ROG Nebula OLED 3K 120Hz, akselerator kecerdasan buatan NPU Ryzen AI, dan bobot luar biasa ringan 1.5 kg.',
                'values' => [
                    'C1' => 26499000,
                    'C2' => 9.6,
                    'C3' => 32,
                    'C4' => 1024,
                    'C5' => 1.50,
                    'C6' => 8.0,
                ],
            ],
            [
                'name' => 'Lenovo Legion Pro 5 16IRX9 (2025/2026)',
                'brand' => 'Lenovo',
                'price' => 27999000,
                'image' => 'images/laptops/dell-inspiron-14.jpg',
                'description' => 'Puncak lini komputasi performa tinggi untuk simulasi komputasi saintifik, model Machine Learning, dan gaming AAA dengan Intel Core i9-14900HX 24-core dan pendingin Legion Coldfront 5.0.',
                'values' => [
                    'C1' => 27999000,
                    'C2' => 9.8,
                    'C3' => 32,
                    'C4' => 1024,
                    'C5' => 2.50,
                    'C6' => 5.5,
                ],
            ],
            [
                'name' => 'HP OMEN Transcend 14 (2025/2026)',
                'brand' => 'HP',
                'price' => 24999000,
                'image' => 'images/laptops/hp-14s.jpg',
                'description' => 'Laptop gaming ringkas 14 inci generasi 2025-2026 berlayar 2.8K 120Hz OLED, keyboard RGB translucent, pendingin vapor chamber, dan prosesor Intel Core Ultra 9 185H berteknologi AI Boost.',
                'values' => [
                    'C1' => 24999000,
                    'C2' => 9.5,
                    'C3' => 32,
                    'C4' => 1024,
                    'C5' => 1.63,
                    'C6' => 7.5,
                ],
            ],
        ];

        foreach ($laptops as $laptopData) {
            $values = $laptopData['values'];
            unset($laptopData['values']);

            $alternative = Alternative::updateOrCreate(
                ['name' => $laptopData['name']],
                $laptopData
            );

            foreach ($values as $criteriaCode => $val) {
                if (isset($criteria[$criteriaCode])) {
                    AlternativeValue::updateOrCreate(
                        [
                            'alternative_id' => $alternative->id,
                            'criteria_id' => $criteria[$criteriaCode]->id,
                        ],
                        [
                            'value' => $val,
                        ]
                    );
                }
            }
        }
    }
}
