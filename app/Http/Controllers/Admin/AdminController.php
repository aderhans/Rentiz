<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function platformAnalytics()
    {
        return view('admin.platform-analytics');
    }
}
