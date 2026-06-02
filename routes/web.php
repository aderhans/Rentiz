<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Penyewa\PenyewaController;
use App\Http\Controllers\Penyedia\PenyediaController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page — sekarang langsung tampilkan form login/register
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    $activeUsers = \App\Models\User::where('role', '!=', 'admin')->count();
    $totalTransaksi = \App\Models\Pesanan::count();
    $rating = '4.8'; // Rating default karena belum ada tabel rating

    return view('landing', [
        'activeTab' => 'login',
        'activeUsers' => $activeUsers,
        'totalTransaksi' => $totalTransaksi,
        'rating' => $rating
    ]);
})->name('landing');

/* --------------------------------------------------------
 * Authentication Routes (Guest Only)
 * -------------------------------------------------------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/verify-notice/{token}', [AuthController::class, 'verifyNotice'])->name('verification.notice');
    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
});

/* --------------------------------------------------------
 * Authenticated Routes
 * -------------------------------------------------------- */
Route::middleware(['auth', 'check.status'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-mode', [AuthController::class, 'switchMode'])->name('switch-mode');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    /* ------------------------------------------------
     * Penyewa Routes (dummy — UI interaktif)
     * ------------------------------------------------ */
    Route::prefix('penyewa')->name('penyewa.')->group(function () {
        Route::get('/cari-barang',      [PenyewaController::class, 'cariBarang'])->name('cari-barang');
        Route::get('/penyewaan-aktif',  [PenyewaController::class, 'penyewaanAktif'])->name('penyewaan-aktif');
        Route::get('/riwayat-sewa',     [PenyewaController::class, 'riwayatSewa'])->name('riwayat-sewa');
        Route::get('/profil',           [PenyewaController::class, 'profil'])->name('profil');
        Route::put('/profil',           [PenyewaController::class, 'updateProfil'])->name('profil.update');
        Route::put('/profil/password',  [PenyewaController::class, 'updatePassword'])->name('profil.password');
        Route::get('/pembayaran',       [PenyewaController::class, 'pembayaran'])->name('pembayaran');
        
        // Keranjang Routes
        Route::get('/keranjang',        [PenyewaController::class, 'keranjang'])->name('keranjang');
        Route::post('/keranjang/tambah',[PenyewaController::class, 'tambahKeranjang'])->name('keranjang.tambah');
        Route::delete('/keranjang/{id}',[PenyewaController::class, 'hapusKeranjang'])->name('keranjang.hapus');
    });

    /* ------------------------------------------------
     * Penyedia Routes (dummy — UI interaktif)
     * ------------------------------------------------ */
    Route::prefix('penyedia')->name('penyedia.')->group(function () {
        Route::get('/daftar-barang',    [PenyediaController::class, 'daftarBarang'])->name('daftar-barang');
        Route::get('/tambah-barang',    [PenyediaController::class, 'tambahBarang'])->name('tambah-barang');
        Route::post('/tambah-barang',   [PenyediaController::class, 'storeBarang'])->name('store-barang');
        Route::get('/request-sewa',     [PenyediaController::class, 'requestSewa'])->name('request-sewa');
        Route::get('/riwayat-transaksi',[PenyediaController::class, 'riwayatTransaksi'])->name('riwayat-transaksi');
        Route::get('/analitik',         [PenyediaController::class, 'analitik'])->name('analitik');
        Route::get('/penarikan-dana',   [PenyediaController::class, 'penarikanDana'])->name('penarikan-dana');
        Route::get('/profil-toko',      [PenyediaController::class, 'profilToko'])->name('profil-toko');
    });

    /* ------------------------------------------------
     * Admin Routes (dummy — UI interaktif)
     * ------------------------------------------------ */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/manajemen-user',   [AdminController::class, 'manajemenUser'])->name('manajemen-user');
        Route::post('/manajemen-user/{id}/suspend', [AdminController::class, 'suspendUser'])->name('suspend-user');
        Route::post('/manajemen-user/{id}/activate', [AdminController::class, 'activateUser'])->name('activate-user');
        Route::delete('/manajemen-user/{id}/delete', [AdminController::class, 'deleteUser'])->name('delete-user');
        Route::get('/semua-listing',    [AdminController::class, 'semuaListing'])->name('semua-listing');
        Route::get('/semua-listing/{id}/detail', [AdminController::class, 'detailBarang'])->name('detail-barang');
        Route::post('/semua-listing/{id}/approve', [AdminController::class, 'approveBarang'])->name('approve-barang');
        Route::post('/semua-listing/{id}/reject',  [AdminController::class, 'rejectBarang'])->name('reject-barang');
        Route::post('/semua-listing/{id}/restore', [AdminController::class, 'restoreBarang'])->name('restore-barang');
        Route::get('/semua-transaksi',  [AdminController::class, 'semuaTransaksi'])->name('semua-transaksi');
        Route::get('/laporan-dispute',  [AdminController::class, 'laporanDispute'])->name('laporan-dispute');
        Route::get('/pengaturan',       [AdminController::class, 'pengaturan'])->name('pengaturan');
        Route::post('/pengaturan/profil', [AdminController::class, 'updateProfil'])->name('update-profil');
        Route::get('/platform-analytics',[AdminController::class, 'platformAnalytics'])->name('platform-analytics');
    });
});
