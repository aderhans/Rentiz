<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function manajemenUser()
    {
        $users = \App\Models\User::where('role', '!=', 'admin')->withCount(['pesananSebagaiPemesan', 'pesananSebagaiPemilik'])->orderBy('created_at', 'desc')->get();
        return view('admin.manajemen-user', compact('users'));
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
        $transactions = \App\Models\Pesanan::with(['pemesan', 'items.barang'])->orderBy('created_at', 'desc')->get();
        return view('admin.semua-transaksi', compact('transactions'));
    }

    public function laporanDispute()
    {
        $cases = \App\Models\Dispute::with(['pelapor', 'terlapor'])->orderBy('created_at', 'desc')->get();
        return view('admin.laporan-dispute', compact('cases'));
    }

    public function takeCase(Request $request, $id)
    {
        $dispute = \App\Models\Dispute::findOrFail($id);
        $dispute->update(['status' => 'under_review']);
        return back()->with('success', 'Berhasil mengambil alih kasus.');
    }

    public function resolveCase(Request $request, $id)
    {
        $dispute = \App\Models\Dispute::findOrFail($id);
        $dispute->update(['status' => 'closed', 'ditutup_at' => now()]);
        return back()->with('success', 'Kasus berhasil diselesaikan.');
    }

    public function pengaturan()
    {
        return view('admin.pengaturan');
    }

    public function platformAnalytics()
    {
        $totalUsers = \App\Models\User::count();
        $totalTransactions = \App\Models\Pesanan::count();
        
        $gmv = \App\Models\Pesanan::where('status', '!=', 'pending_payment')->sum('total_biaya');
        $revenue = $gmv * 0.10; // Asumsi komisi platform 10%
        
        $kategoriCount = \App\Models\Barang::select('kategori_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('kategori_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $kotaCount = \App\Models\Barang::select('kota', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('kota')
            ->orderByDesc('total')
            ->take(3)
            ->get();

        return view('admin.platform-analytics', compact('totalUsers', 'totalTransactions', 'gmv', 'revenue', 'kategoriCount', 'kotaCount'));
    }
}
