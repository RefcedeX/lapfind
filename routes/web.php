<?php

use App\Http\Controllers\Admin\AlternativeController;
use App\Http\Controllers\Admin\AlternativeValueController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CriterionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpkController;
use App\Models\Alternative;
use App\Models\Criterion;
use Illuminate\Support\Facades\Route;

// Rute Publik
Route::get('/', [HomeController::class, 'index'])->name('home');

// Katalog & Komparasi Laptop
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{alternative}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/bandingkan', [CatalogController::class, 'compare'])->name('catalog.compare');

// Alur SPK 3 Langkah (AHP -> Uji Konsistensi -> TOPSIS -> Hasil)
Route::prefix('spk')->name('spk.')->group(function () {
    Route::get('/', [SpkController::class, 'index'])->name('index');
    Route::post('/ahp', [SpkController::class, 'processAhp'])->name('process_ahp');
    Route::get('/bobot', [SpkController::class, 'showWeights'])->name('weights');
    Route::post('/topsis', [SpkController::class, 'processTopsis'])->name('process_topsis');
    Route::get('/hasil/{spkResult?}', [SpkController::class, 'showResult'])->name('result');
    Route::get('/ekspor-pdf/{spkResult?}', [SpkController::class, 'exportPdf'])->name('export_pdf');
});

// Riwayat Hasil Pengguna Terdaftar
Route::middleware('auth')->group(function () {
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history.index');
    Route::delete('/riwayat/{spkResult}', [HistoryController::class, 'destroy'])->name('history.destroy');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $latestResult = $user->spkResults()->latest()->first();
    $savedCount = $user->spkResults()->count();
    $totalAlternatives = Alternative::count();
    $criteria = Criterion::orderBy('code')->get();
    $featuredAlternatives = Alternative::with('values.criterion')->latest()->take(3)->get();

    return view('dashboard', compact('user', 'latestResult', 'savedCount', 'totalAlternatives', 'criteria', 'featuredAlternatives'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rute Administrasi SPK (Khusus Role Admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('criteria', CriterionController::class);

    // Laptop Alternatives & Export
    Route::get('alternatives/export', [AlternativeController::class, 'export'])->name('alternatives.export');
    Route::resource('alternatives', AlternativeController::class);

    // Decision Matrix & Export
    Route::get('values/export', [AlternativeValueController::class, 'export'])->name('values.export');
    Route::get('values', [AlternativeValueController::class, 'index'])->name('values.index');
    Route::put('values', [AlternativeValueController::class, 'update'])->name('values.update');
    Route::resource('users', UserController::class);

    // Audit Log & Activity Tracking
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});

require __DIR__.'/auth.php';
