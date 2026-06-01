<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function manajemenUser()
    {
        return view('admin.manajemen-user');
    }

    public function semuaListing()
    {
        $items = \App\Models\Barang::with('user')->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")->orderBy('created_at', 'desc')->get();
        return view('admin.semua-listing', compact('items'));
    }

    public function approveBarang(Request $request, $id)
    {
        $item = \App\Models\Barang::findOrFail($id);
        $item->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Barang berhasil diverifikasi (Disetujui).');
    }

    public function rejectBarang(Request $request, $id)
    {
        $item = \App\Models\Barang::findOrFail($id);
        $item->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Barang telah ditolak.');
    }

    public function semuaTransaksi()
    {
        return view('admin.semua-transaksi');
    }

    public function laporanDispute()
    {
        return view('admin.laporan-dispute');
    }

    public function pengaturan()
    {
        return view('admin.pengaturan');
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $path = $request->file('foto_profil')->store('profiles', 'public');
            $user->update(['foto_profil' => $path]);
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function platformAnalytics()
    {
        return view('admin.platform-analytics');
    }
}
