<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    /**
     * Halaman Cari Barang (dummy)
     */
    public function cariBarang(Request $request)
    {
        $query = \App\Models\Barang::with('user', 'fotos')->where('status', 'active');

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('kota')) {
            $query->where('kota', 'like', '%' . $request->kota . '%');
        }

        $items = $query->latest()->get();

        return view('penyewa.cari-barang', [
            'items'    => $items,
            'query'    => $request->get('q', ''),
            'kategori' => $request->get('kategori', 'semua'),
            'kota'     => $request->get('kota', ''),
        ]);
    }

    /**
     * Halaman Penyewaan Aktif (dummy)
     */
    public function penyewaanAktif()
    {
        return view('penyewa.penyewaan-aktif');
    }

    /**
     * Halaman Riwayat Sewa (dummy)
     */
    public function riwayatSewa()
    {
        return view('penyewa.riwayat-sewa');
    }

    /**
     * Halaman Wishlist (dummy)
     */
    public function wishlist()
    {
        return view('penyewa.wishlist');
    }

    /**
     * Halaman Profil Saya (dummy)
     */
    public function profil()
    {
        return view('penyewa.profil');
    }

    /**
     * Halaman Pembayaran (dummy)
     */
    public function pembayaran()
    {
        return view('penyewa.pembayaran');
    }
}
