# SPK Pemilihan Laptop untuk Mahasiswa
### Sistem Pendukung Keputusan Berbasis Kombinasi Metode AHP & TOPSIS

Aplikasi web Sistem Pendukung Keputusan (SPK) untuk menentukan rekomendasi laptop terbaik bagi mahasiswa berdasarkan preferensi dan profil kebutuhan akademis. Sistem ini dikembangkan untuk memenuhi tugas mata kuliah **Manajemen Sains** (fokus pada transparansi dan ketepatan perhitungan matematis SPK) dan **Rekayasa Web** (fokus pada arsitektur sistem perangkat lunak, pola MVC modern, keamanan, dan *User Experience* editorial).

---

## Daftar Isi
1. [Fitur Utama](#fitur-utama)
2. [Tech Stack](#tech-stack)
3. [Kredensial Pengguna Demo](#kredensial-pengguna-demo)
4. [Panduan Instalasi & Menjalankan Lokal](#panduan-instalasi--menjalankan-lokal)
5. [Landasan Teori & Rumus Matematis SPK](#landasan-teori--rumus-matematis-spk)
   - [AHP (Analytic Hierarchy Process)](#1-metode-ahp-pembobotan-kriteria)
   - [TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)](#2-metode-topsis-perankingan-alternatif)
6. [Struktur Arsitektur & Organisasi Kode](#struktur-arsitektur--organisasi-kode)
7. [Pengujian Otomatis (Automated Testing)](#pengujian-otomatis-automated-testing)

---

## Fitur Utama

- **Katalog Laptop & Komparasi Berdampingan**:
  - Penjelajahan 12 alternatif laptop populer mahasiswa dengan filter merek dan rentang harga resmi.
  - Fitur komparasi berdampingan (*side-by-side*) hingga 4 laptop sekaligus.
- **Wizard SPK 3 Langkah yang Transparan**:
  - **Langkah 1**: Input perbandingan berpasangan AHP Skala Saaty (1–9) atau pilihan cepat 3 prasetel mahasiswa (*Umum*, *Coding/Teknik*, *Desain Grafis*).
  - **Langkah 2**: Tinjauan vektor bobot prioritas kriteria ($w_j$), nilai $\lambda_{\max}$, *Consistency Index* ($CI$), dan verifikasi *Consistency Ratio* ($CR \le 0.10$).
  - **Langkah 3**: Hasil pemeringkatan alternatif TOPSIS dilengkapi podium juara, visualisasi skor kedekatan relatif ($V_i$), tombol komparasi 3 laptop teratas, dan tabel audit kalkulasi antara ($R$, $Y$, $A^+$, $A^-$, $D^+$, $D^-$).
- **Ekspor Dokumen Laporan Resmi (PDF)**:
  - Unduh laporan hasil evaluasi dan tabel audit perhitungan ke dalam format PDF berstandar A4 menggunakan `barryvdh/laravel-dompdf`.
- **Manajemen Riwayat Evaluasi**:
  - Pengguna yang masuk (*authenticated*) otomatis tersimpan riwayat evaluasinya dan dapat meninjau atau mencetak ulang kapan saja.
- **Panel Administrasi Khusus (Admin CRUD)**:
  - Dashboard statistik ringkasan sistem.
  - CRUD Kriteria & penetapan atribut (*benefit*/*cost*).
  - CRUD Alternatif Laptop & unggah gambar/harga resmi.
  - Matriks Nilai Alternatif (*Decision Matrix Grid Editor*) untuk mengedit nilai matriks keputusan secara massal (*bulk update*).
  - Manajemen Pengguna (*User Management*) dengan pengaman anti-hapus diri sendiri (*self-deletion guard*).
- **Desain & Antarmuka Editorial**:
  - Mengusung palet warna *warm canvas* (`#FAF9F6`), teks *deep ink* (`#1C1B19`), dan aksen *forest green* (`#1F5C4A`).
  - Drawer navigasi responsif dengan pola *Push-Back Depth* dan latar gelap.

---

## Tech Stack

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 13.x | Arsitektur MVC bersih, PHP 8.4, type-hinting ketat |
| **Database** | MySQL | Relasi Eloquent, Migration, dan Seeder |
| **Autentikasi** | Laravel Breeze (Blade) | Login, Register, Password Reset, Profile Management |
| **Frontend Styling** | Tailwind CSS v4 | Desain custom editorial tanpa dependensi template luar |
| **Interaktivitas UI** | Alpine.js & Vanilla JS | Matrix recalculation preview, perbandingan slider, drawer |
| **Ekspor Dokumen** | `barryvdh/laravel-dompdf` v3.1 | Rendering cetak laporan PDF format A4 |
| **Pengujian** | PHPUnit | 52 Automated Tests (Unit + Feature) 100% lulus |

---

## Kredensial Pengguna Demo

Aplikasi telah dilengkapi seeder bawaan dengan dua akun pengujian:

| Peran (Role) | Email | Kata Sandi | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@spk-laptop.test` | `password` | Akses penuh Panel Admin (`/admin`), kelola kriteria, laptop, matriks nilai, pengguna |
| **Mahasiswa** | `mahasiswa@spk-laptop.test` | `password` | Akses publik, evaluasi SPK, simpan dan cetak riwayat evaluasi |

*Pengguna tamu tanpa login (Guest) juga dapat menjalankan simulasi evaluasi SPK dan mengunduh laporan PDF.*

---

## Panduan Instalasi & Menjalankan Lokal

### Prasyarat Sistem
- PHP >= 8.2 (disarankan PHP 8.4) dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `gd`
- Composer 2.x
- Node.js >= 18.x & NPM
- MySQL Server (misal via XAMPP atau Laragon pada port 3306)

### Langkah-Langkah Instalasi

1. **Clone atau Buka Direktori Proyek**:
   ```bash
   cd "d:/Semester 5/Tugas semester 5 UTS/Website Pemilihan Kriteria Laptop Bagi mahasiswa"
   ```

2. **Pasang Dependensi PHP (Composer)**:
   ```bash
   composer install
   ```

3. **Pasang Dependensi Frontend (NPM)**:
   ```bash
   npm install
   ```

4. **Konfigurasi Lingkungan (`.env`)**:
   Salin berkas contoh jika belum ada `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Pastikan konfigurasi koneksi basis data di `.env` sudah sesuai dengan server MySQL lokal Anda:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=spk_laptop
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Buat Database & Jalankan Migrasi + Seeder**:
   Buat database `spk_laptop` di MySQL, lalu jalankan migrasi beserta seluruh data seeder awal:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Perintah ini akan membuat tabel, mengisi akun admin & mahasiswa, 6 kriteria akademis, dan 12 data laptop lengkap dengan matriks nilainya.*

6. **Kompilasi Aset Frontend**:
   ```bash
   npm run build
   ```
   *Atau jalankan `npm run dev` jika ingin mengaktifkan Vite hot module reload saat pengembangan.*

7. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui peramban web pada alamat:
   **`http://127.0.0.1:8000`**

---

## Landasan Teori & Rumus Matematis SPK

Aplikasi ini menggabungkan dua metode pengambilan keputusan multi-kriteria (*Multi-Criteria Decision Making*):
1. **AHP (Analytic Hierarchy Process)** oleh Thomas L. Saaty untuk menentukan **vektor bobot kepentingan kriteria** ($w_j$) secara konsisten.
2. **TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)** oleh Hwang & Yoon untuk melakukan **pemeringkatan alternatif** berdasarkan kedekatan relatif terhadap solusi ideal.

```
+-------------------------------------------------------------+
|                     6 KRITERIA EVALUASI                     |
| C1: Harga (Cost)                C4: Penyimpanan SSD (Benefit)|
| C2: Performa CPU (Benefit)      C5: Bobot Fisik (Cost)      |
| C3: Kapasitas RAM (Benefit)     C6: Ketahanan Baterai (Ben.)|
+-------------------------------------------------------------+
                              |
                              v
             [ METODE AHP - Analytic Hierarchy Process ]
             1. Matriks Perbandingan Berpasangan (Skala 1-9)
             2. Normalisasi Matriks Kolom
             3. Vektor Bobot Prioritas Kriteria (w)
             4. Uji Konsistensi: λ_max -> CI -> CR <= 0.10
                              |
                              v (Bobot w_j)
             [ METODE TOPSIS - Ranking Alternatif ]
             1. Matriks Keputusan X (12 Laptop x 6 Kriteria)
             2. Normalisasi Vektor Pembagi: r_ij = x_ij / √(Σ x_kj²)
             3. Matriks Terbobot: y_ij = w_j * r_ij
             4. Solusi Ideal Positif (A+) & Negatif (A-)
             5. Jarak Euclidean: D_i+ dan D_i-
             6. Skor Kedekatan Relatif: V_i = D_i- / (D_i+ + D_i-)
                              |
                              v
                 [ HASIL PERINGKAT REKOMENDASI ]
```

---

### 1. Metode AHP (Pembobotan Kriteria)

#### a. Matriks Perbandingan Berpasangan ($A$)
Untuk $n$ kriteria, matriks perbandingan $A = [a_{ij}]_{n \times n}$ disusun menggunakan Skala Saaty (1–9). Matriks memiliki sifat resiprokal:
$$a_{ij} > 0, \quad a_{ii} = 1, \quad a_{ji} = \frac{1}{a_{ij}}$$

#### b. Normalisasi Matriks Kolom ($\bar{A}$)
Setiap elemen dibagi dengan jumlah total pada kolom yang bersangkutan:
$$\bar{a}_{ij} = \frac{a_{ij}}{\sum_{k=1}^n a_{kj}}$$

#### c. Vektor Bobot Prioritas Kriteria ($w$)
Bobot kriteria ke-$i$ dihitung sebagai rata-rata baris dari matriks yang telah dinormalisasi:
$$w_i = \frac{1}{n} \sum_{j=1}^n \bar{a}_{ij}, \quad \text{dengan } \sum_{i=1}^n w_i = 1$$

#### d. Pengujian Rasio Konsistensi (*Consistency Ratio / CR*)
1. Hitung vektor konsistensi $v = A \cdot w$
2. Hitung nilai eigen maksimum ($\lambda_{\max}$):
   $$\lambda_{\max} = \frac{1}{n} \sum_{i=1}^n \frac{(A \cdot w)_i}{w_i}$$
3. Hitung Indeks Konsistensi (*Consistency Index / CI*):
   $$CI = \frac{\lambda_{\max} - n}{n - 1}$$
4. Ambil Indeks Acak (*Random Index / RI*) Saaty berdasarkan ukuran matriks ($n$):

| $n$ | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12 | 13 | 14 | 15 |
| :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **RI** | 0.00 | 0.00 | 0.58 | 0.90 | 1.12 | 1.24 | 1.32 | 1.41 | 1.45 | 1.49 | 1.51 | 1.48 | 1.56 | 1.57 | 1.59 |

5. Hitung Rasio Konsistensi (*Consistency Ratio / CR*):
   $$CR = \frac{CI}{RI}$$
   > **Kaidah Keputusan Konsistensi**:
   > Jika $CR \le 0.10$ (10%), matriks perbandingan dinilai **KONSISTEN** dan bobot prioritas valid untuk digunakan pada proses TOPSIS. Jika $CR > 0.10$, preferensi perlu ditinjau ulang.

---

### 2. Metode TOPSIS (Perankingan Alternatif)

#### a. Matriks Keputusan ($X$)
Matriks berukuran $m \times n$ di mana $m$ adalah jumlah alternatif laptop (12 unit) dan $n$ adalah kriteria (6 kriteria):
$$X = \begin{bmatrix} x_{11} & x_{12} & \dots & x_{1n} \\ x_{21} & x_{22} & \dots & x_{2n} \\ \vdots & \vdots & \ddots & \vdots \\ x_{m1} & x_{m2} & \dots & x_{mn} \end{bmatrix}$$

#### b. Normalisasi Vektor ($R$)
Setiap elemen $x_{ij}$ dinormalisasi menjadi $r_{ij}$ menggunakan akar jumlah kuadrat kolom:
$$r_{ij} = \frac{x_{ij}}{\sqrt{\sum_{k=1}^m x_{kj}^2}}$$

#### c. Matriks Keputusan Ternormalisasi Terbobot ($Y$)
Elemen ternormalisasi dikalikan dengan bobot kriteria $w_j$ yang diperoleh dari metode AHP:
$$y_{ij} = w_j \cdot r_{ij}$$

#### d. Penentuan Solusi Ideal Positif ($A^+$) dan Solusi Ideal Negatif ($A^-$)
Berdasarkan jenis kriteria (*Benefit* atau *Cost*):
- **Solusi Ideal Positif ($A^+$)**:
  $$y_j^+ = \begin{cases} \max_{i} (y_{ij}), & \text{jika } j \text{ adalah kriteria Benefit} \\ \min_{i} (y_{ij}), & \text{jika } j \text{ adalah kriteria Cost (Harga, Bobot)} \end{cases}$$
  $$A^+ = \{ y_1^+, y_2^+, \dots, y_n^+ \}$$

- **Solusi Ideal Negatif ($A^-$)**:
  $$y_j^- = \begin{cases} \min_{i} (y_{ij}), & \text{jika } j \text{ adalah kriteria Benefit} \\ \max_{i} (y_{ij}), & \text{jika } j \text{ adalah kriteria Cost (Harga, Bobot)} \end{cases}$$
  $$A^- = \{ y_1^-, y_2^-, \dots, y_n^- \}$$

#### e. Jarak Solusi Euclidean ($D^+$ dan $D^-$)
Jarak setiap alternatif terhadap solusi ideal positif ($D_i^+$) dan solusi ideal negatif ($D_i^-$):
$$D_i^+ = \sqrt{\sum_{j=1}^n (y_{ij} - y_j^+)^2}$$
$$D_i^- = \sqrt{\sum_{j=1}^n (y_{ij} - y_j^-)^2}$$

#### f. Nilai Preferensi Relatif Alternatif ($V_i$)
Kedekatan relatif setiap alternatif laptop terhadap solusi ideal:
$$V_i = \frac{D_i^-}{D_i^+ + D_i^-}, \quad \text{di mana } 0 \le V_i \le 1$$

Alternatif dengan nilai $V_i$ terbesar merupakan rekomendasi laptop paling ideal.

---

## Struktur Arsitektur & Organisasi Kode

Proyek ini dibangun dengan menerapkan prinsip *Single Responsibility* dan pemisahan logika komputasi matematis ke dalam lapisan *Service*:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php               # Beranda & ringkasan kriteria
│   │   ├── CatalogController.php            # Katalog laptop, filter, detail, komparasi
│   │   ├── SpkController.php                # Wizard SPK 3 langkah & ekspor PDF
│   │   ├── HistoryController.php            # Riwayat evaluasi pengguna
│   │   └── Admin/
│   │       ├── DashboardController.php      # Ringkasan analitik admin
│   │       ├── CriterionController.php      # CRUD kriteria & tipe
│   │       ├── AlternativeController.php    # CRUD laptop & gambar
│   │       ├── AlternativeValueController.php # Bulk decision matrix grid
│   │       └── UserController.php           # Manajemen pengguna
│   ├── Middleware/
│   │   └── EnsureUserIsAdmin.php            # Proteksi akses peran admin
│   └── Requests/
│       ├── Admin/                           # Validasi data dengan pesan Bahasa Indonesia
│       └── SpkAhpRequest.php                # Validasi perbandingan berpasangan
├── Models/
│   ├── User.php                             # Model akun dengan helper isAdmin()
│   ├── Criterion.php                        # Kriteria C1-C6 (type: benefit/cost)
│   ├── Alternative.php                      # Alternatif laptop & accessor formatted_price
│   ├── AlternativeValue.php                 # Nilai alternatif per kriteria (matriks X)
│   ├── AhpComparison.php                    # Rekaman matriks perbandingan
│   └── SpkResult.php                        # Snapshot evaluasi & langkah antara JSON
└── Services/
    ├── AhpService.php                       # Mesin kalkulasi murni AHP & uji konsistensi
    └── TopsisService.php                    # Mesin kalkulasi murni TOPSIS & snapshot audit
resources/views/
├── layouts/
│   ├── public.blade.php                     # Layout publik editorial & drawer depth
│   └── admin.blade.php                      # Layout dashboard administrator
├── catalog/                                 # Antarmuka katalog & komparasi
├── spk/
│   ├── step1_criteria.blade.php             # Input perbandingan AHP & prasetel
│   ├── step2_weights.blade.php              # Tinjauan bobot & indikator CR
│   ├── step3_result.blade.php               # Pemeringkatan & tabel audit lengkap
│   └── pdf_report.blade.php                 # Template cetak PDF formal A4
├── history/                                 # Riwayat evaluasi pengguna
└── admin/                                   # Antarmuka panel admin CRUD
tests/
├── Unit/
│   ├── AhpServiceTest.php                   # Pengujian matematis AHP (10 unit tests)
│   └── TopsisServiceTest.php                # Pengujian matematis TOPSIS (8 unit tests)
└── Feature/
    ├── AdminTest.php                        # Pengujian keamanan & CRUD Admin (11 tests)
    └── PublicAndSpkFlowTest.php             # Pengujian alur SPK & ekspor PDF (8 tests)
```

---

## Pengujian Otomatis (Automated Testing)

Seluruh logika matematika, controller, middleware, dan alur autentikasi telah diuji secara komprehensif menggunakan PHPUnit:

```bash
php artisan test
```

### Ringkasan Status Pengujian:
```text
Pass: 52 tests
Assertions: 228 assertions
Duration: ~2.6 detik
Coverage:
- AhpServiceTest: Validasi sifat resiprokal matriks, bobot prioritas sum = 1, CI, CR Saaty, prasetel profil.
- TopsisServiceTest: Validasi normalisasi vektor R, normalisasi terbobot Y, solusi ideal benefit/cost, jarak Euclidean, nilai preferensi Vi [0, 1].
- AdminTest: Proteksi rute admin, CRUD Kriteria, CRUD Alternatif, Bulk Editor Matriks Nilai, Self-Deletion Guard.
- PublicAndSpkFlowTest: Beranda, katalog & komparasi, alur wizard 3 langkah guest & login, simpan riwayat, ekspor PDF.
```

---

## Lisensi
Proyek aplikasi ini dikembangkan untuk keperluan akademik dan perkuliahan. Dirilis di bawah lisensi [MIT License](LICENSE).
