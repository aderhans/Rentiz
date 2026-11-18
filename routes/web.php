<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Penyewa\PenyewaController;
use App\Http\Controllers\Penyedia\PenyediaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SystemMediaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Endpoint Keep-Alive (UptimeRobot / Cron-job) & Self-Healing Persistent Storage
Route::get('/ping', [SystemMediaController::class, 'ping'])->name('system.ping');
Route::get('/storage/{path}', [SystemMediaController::class, 'serveStorage'])
    ->where('path', '.*')
    ->name('storage.fallback');

// Landing page — sekarang langsung tampilkan form login/register
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing', ['activeTab' => 'login']);
})->name('landing');

/* --------------------------------------------------------
 * Authentication Routes (Guest Only)
 * -------------------------------------------------------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/* --------------------------------------------------------
 * Authenticated Routes
 * -------------------------------------------------------- */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-mode', [AuthController::class, 'switchMode'])->name('switch-mode');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    /* ------------------------------------------------
     * Email Verification Routes
     * ------------------------------------------------ */
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('info', 'Link verifikasi telah dikirim ulang ke email Anda.');
    })->middleware('throttle:6,1')->name('verification.send');

    /* ------------------------------------------------
     * Penyewa Routes
     * ------------------------------------------------ */
    Route::prefix('penyewa')->name('penyewa.')->group(function () {
        Route::get('/cari-barang',      [PenyewaController::class, 'cariBarang'])->name('cari-barang');
        Route::get('/penyewaan-aktif',  [PenyewaController::class, 'penyewaanAktif'])->name('penyewaan-aktif');
        Route::get('/penyewaan-aktif/{id}/checklist', [PenyewaController::class, 'formChecklist'])->name('penyewaan-aktif.checklist');
        Route::post('/penyewaan-aktif/{id}/checklist', [PenyewaController::class, 'simpanChecklist'])->name('penyewaan-aktif.checklist.simpan');
        Route::get('/riwayat-sewa',     [PenyewaController::class, 'riwayatSewa'])->name('riwayat-sewa');
        Route::get('/profil',           [PenyewaController::class, 'profil'])->name('profil');
        Route::put('/profil',           [PenyewaController::class, 'updateProfil'])->name('profil.update');
        Route::put('/profil/password',  [PenyewaController::class, 'updatePassword'])->name('profil.password');
        Route::get('/pembayaran',       [PenyewaController::class, 'pembayaran'])->name('pembayaran');
        Route::post('/pembayaran/{id}/proses', [PenyewaController::class, 'prosesBayar'])->name('pembayaran.proses');
        Route::get('/pembayaran/{id}/gateway', [PenyewaController::class, 'showPaymentGateway'])->name('pembayaran.gateway');
        Route::post('/pembayaran/{id}/batalkan', [PenyewaController::class, 'batalkanPesanan'])->name('pembayaran.batalkan');
        Route::post('/wishlist/toggle', [PenyewaController::class, 'toggleWishlist'])->name('wishlist.toggle');
        Route::post('/proses-sewa',     [PenyewaController::class, 'prosesSewa'])->name('proses-sewa');

        // Keranjang Routes
        Route::get('/keranjang',        [PenyewaController::class, 'keranjang'])->name('keranjang');
        Route::post('/keranjang/tambah',[PenyewaController::class, 'tambahKeranjang'])->name('keranjang.tambah');
        Route::put('/keranjang/{id}/tanggal', [PenyewaController::class, 'updateTanggalKeranjang'])->name('keranjang.update-tanggal');
        Route::delete('/keranjang/{id}',[PenyewaController::class, 'hapusKeranjang'])->name('keranjang.hapus');
        Route::get('/keranjang/checkout', [PenyewaController::class, 'checkout'])->name('keranjang.checkout');
        Route::post('/keranjang/checkout/proses', [PenyewaController::class, 'prosesCheckout'])->name('keranjang.checkout.proses');

        // Detail Barang Route
        Route::get('/barang/{id}', [PenyewaController::class, 'detailBarang'])->name('barang.detail');
    });

    /* ------------------------------------------------
     * Penyedia Routes (dummy — UI interaktif)
     * ------------------------------------------------ */
    Route::prefix('penyedia')->name('penyedia.')->group(function () {
        Route::get('/daftar-barang',    [PenyediaController::class, 'daftarBarang'])->name('daftar-barang');
        Route::get('/tambah-barang',    [PenyediaController::class, 'tambahBarang'])->name('tambah-barang');
        Route::post('/tambah-barang',   [PenyediaController::class, 'storeBarang'])->name('store-barang');
        Route::patch('/barang/{id}',    [PenyediaController::class, 'updateBarang'])->name('update-barang');
        Route::delete('/barang/{id}',   [PenyediaController::class, 'destroy'])->name('delete-barang');
        Route::get('/request-sewa',     [PenyediaController::class, 'requestSewa'])->name('request-sewa');
        Route::get('/request-sewa/{id}/pengembalian', [PenyediaController::class, 'formChecklistPengembalian'])->name('request-sewa.pengembalian');
        Route::post('/request-sewa/{id}/pengembalian', [PenyediaController::class, 'simpanChecklistPengembalian'])->name('request-sewa.pengembalian.simpan');
        Route::get('/riwayat-transaksi',[PenyediaController::class, 'riwayatTransaksi'])->name('riwayat-transaksi');
        Route::get('/analitik',         [PenyediaController::class, 'analitik'])->name('analitik');
        Route::get('/penarikan-dana',   [PenyediaController::class, 'penarikanDana'])->name('penarikan-dana');
        Route::post('/penarikan-dana',  [PenyediaController::class, 'prosesPenarikanDana'])->name('penarikan-dana.proses');
        Route::get('/profil-toko',      [PenyediaController::class, 'profilToko'])->name('profil-toko');
    });

    /* ------------------------------------------------
     * Admin Routes (dummy — UI interaktif)
     * ------------------------------------------------ */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/manajemen-user',   [AdminController::class, 'manajemenUser'])->name('manajemen-user');
        Route::get('/semua-listing',    [AdminController::class, 'semuaListing'])->name('semua-listing');
        Route::post('/semua-listing/{id}/approve', [AdminController::class, 'approveBarang'])->name('approve-barang');
        Route::post('/semua-listing/{id}/reject',  [AdminController::class, 'rejectBarang'])->name('reject-barang');
        Route::get('/semua-transaksi',  [AdminController::class, 'semuaTransaksi'])->name('semua-transaksi');
        Route::get('/laporan-dispute',  [AdminController::class, 'laporanDispute'])->name('laporan-dispute');
        Route::get('/pengaturan',       [AdminController::class, 'pengaturan'])->name('pengaturan');
        Route::get('/platform-analytics',[AdminController::class, 'platformAnalytics'])->name('platform-analytics');
        Route::post('/user/{id}/suspend',  [AdminController::class, 'suspendUser'])->name('suspend-user');
        Route::post('/user/{id}/activate', [AdminController::class, 'activateUser'])->name('activate-user');
    });
});
