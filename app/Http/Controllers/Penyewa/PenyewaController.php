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
            $query->where('kota', $request->kota);
        }

        $items = $query->latest()->get();

        $cities = \App\Models\Barang::select('kota')->distinct()->whereNotNull('kota')->pluck('kota');

        return view('penyewa.cari-barang', [
            'items'    => $items,
            'cities'   => $cities,
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
     * Halaman Profil Saya (dummy)
     */
    public function profil()
    {
        return view('penyewa.profil');
    }

    /**
     * Update Data Profil (Nama & Kontak)
     */
    public function updateProfil(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        
        $name = trim($request->first_name . ' ' . $request->last_name);
        
        $user->name = $name;
        $user->phone = $request->phone;
        $user->save();

        \Illuminate\Support\Facades\DB::table('log')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'aksi' => 'Update Profil',
            'detail' => 'User memperbarui data profil (nama/kontak).',
            'ip_address' => $request->ip(),
            'terjadi_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah.'])->with('active_tab', 'security');
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        \Illuminate\Support\Facades\DB::table('log')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'aksi' => 'Update Password',
            'detail' => 'User memperbarui password.',
            'ip_address' => $request->ip(),
            'terjadi_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui!')->with('active_tab', 'security');
    }

    /**
     * Halaman Pembayaran (dummy)
     */
    public function pembayaran()
    {
        return view('penyewa.pembayaran');
    }

    /* --------------------------------------------------------
     * Keranjang Methods
     * -------------------------------------------------------- */

    public function keranjang()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $keranjangs = \App\Models\Keranjang::with(['barang.user', 'barang.fotos'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Kelompokkan berdasarkan penyedia (user dari barang)
        $grouped = $keranjangs->groupBy(function ($item) {
            return $item->barang->user->id;
        });

        return view('penyewa.keranjang', compact('grouped'));
    }

    public function tambahKeranjang(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        $barang = \App\Models\Barang::findOrFail($request->barang_id);
        if ($barang->user_id == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menyewa atau memasukkan barang milik Anda sendiri ke keranjang.');
        }

        // Cek apakah barang sudah ada di keranjang
        $existing = \App\Models\Keranjang::where('user_id', $user->id)
            ->where('barang_id', $request->barang_id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('info', 'Barang sudah ada di keranjang.');
        }

        \App\Models\Keranjang::create([
            'user_id' => $user->id,
            'barang_id' => $request->barang_id,
        ]);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan ke keranjang!');
    }

    public function hapusKeranjang($id)
    {
        $keranjang = \App\Models\Keranjang::where('id', $id)->where('user_id', \Illuminate\Support\Facades\Auth::id())->firstOrFail();
        $keranjang->delete();

        return redirect()->back()->with('success', 'Barang dihapus dari keranjang.');
    }
}
