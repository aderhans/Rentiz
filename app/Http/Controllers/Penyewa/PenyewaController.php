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
}
