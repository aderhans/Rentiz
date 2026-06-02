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
        $usersQuery = \App\Models\User::where('role', '!=', 'admin')->orderBy('created_at', 'desc');
        $users = $usersQuery->paginate(10);
        
        $totalUser = \App\Models\User::where('role', '!=', 'admin')->count();
        $totalAktif = \App\Models\User::where('role', '!=', 'admin')->where('status', 'active')->count();
        $totalSuspended = \App\Models\User::where('role', '!=', 'admin')->where('status', 'suspended')->count();
        $mingguIni = \App\Models\User::where('role', '!=', 'admin')->where('created_at', '>=', now()->subWeek())->count();
        
        $pesananCounts = \Illuminate\Support\Facades\DB::table('pesanan')
            ->selectRaw('pemesan_id as user_id, count(*) as total')
            ->groupBy('pemesan_id')
            ->get()
            ->pluck('total', 'user_id')
            ->toArray();
            
        $pesananPemilikCounts = \Illuminate\Support\Facades\DB::table('pesanan')
            ->selectRaw('pemilik_id as user_id, count(*) as total')
            ->groupBy('pemilik_id')
            ->get()
            ->pluck('total', 'user_id')
            ->toArray();

        foreach ($users as $user) {
            $user->trx = ($pesananCounts[$user->id] ?? 0) + ($pesananPemilikCounts[$user->id] ?? 0);
        }
        
        return view('admin.manajemen-user', compact('users', 'totalUser', 'totalAktif', 'totalSuspended', 'mingguIni'));
    }

    public function suspendUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        if ($user->isAdmin()) {
            return redirect()->back()->withErrors(['msg' => 'Tidak bisa men-suspend akun administrator.']);
        }
        $user->update(['status' => 'suspended']);
        return redirect()->back()->with('success', 'Akun ' . $user->name . ' berhasil di-suspend!');
    }

    public function activateUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Akun ' . $user->name . ' berhasil diaktifkan!');
    }

    public function deleteUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        if ($user->isAdmin()) {
            return redirect()->back()->withErrors(['msg' => 'Tidak bisa menghapus akun administrator.']);
        }
        $user->delete();
        return redirect()->back()->with('success', 'Akun ' . $user->name . ' berhasil dihapus permanen!');
    }

    public function semuaListing(Request $request)
    {
        $query = \App\Models\Barang::with(['user', 'fotos', 'kategori'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc');

        // Filter status
        if ($request->filled('status') && $request->status !== 'semua') {
            $statusMap = [
                'aktif'    => 'active',
                'pending'  => 'pending',
                'nonaktif' => ['rejected', 'suspended', 'unavailable'],
            ];
            $mapped = $statusMap[$request->status] ?? null;
            if ($mapped) {
                if (is_array($mapped)) {
                    $query->whereIn('status', $mapped);
                } else {
                    $query->where('status', $mapped);
                }
            }
        }

        // Filter kategori
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter search (nama)
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $items    = $query->paginate(10)->withQueryString();
        $kategoris = \Illuminate\Support\Facades\DB::table('kategori')->orderBy('nama')->get();

        // Hitung statistik dari semua data (tanpa filter)
        $allItems = \App\Models\Barang::selectRaw(
            'count(*) as total,
             sum(status = "active") as aktif,
             sum(status = "pending") as pending,
             sum(status NOT IN ("active","pending")) as lainnya'
        )->first();

        return view('admin.semua-listing', compact('items', 'kategoris', 'allItems'));
    }

    public function detailBarang($id)
    {
        $barang = \App\Models\Barang::with(['user', 'fotos', 'kategori'])->findOrFail($id);
        return view('admin.detail-barang', compact('barang'));
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

    public function restoreBarang(Request $request, $id)
    {
        $item = \App\Models\Barang::findOrFail($id);
        // Kembalikan ke pending agar admin bisa meninjau ulang sebelum disetujui
        $item->update(['status' => 'pending']);
        return redirect()->back()->with('success', 'Barang ' . $item->nama . ' telah dipulihkan ke antrian pending dan menunggu tinjauan ulang.');
    }

    public function semuaTransaksi()
    {
        $transactions = \App\Models\Pesanan::with(['pemesan', 'pemilik', 'items.barang'])->orderBy('created_at', 'desc')->paginate(10);

        $totalTrx = \App\Models\Pesanan::count();
        $totalGMV = \App\Models\Pesanan::whereNotIn('status', ['cancelled', 'refunded', 'pending_payment'])->sum('total_biaya');
        $platformFee = $totalGMV * 0.1;
        $trxBulanIni = \App\Models\Pesanan::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
        $trxDispute = \App\Models\Pesanan::where('status', 'disputed')->count();

        return view('admin.semua-transaksi', compact('transactions', 'totalTrx', 'totalGMV', 'platformFee', 'trxBulanIni', 'trxDispute'));
    }

    public function laporanDispute()
    {
        $cases = \App\Models\Dispute::with(['pelapor', 'terlapor'])->orderBy('created_at', 'desc')->get();
        $totalCases = $cases->count();
        $kasusBaru = $cases->where('status', 'open')->count();
        $kasusProses = $cases->whereIn('status', ['under_review', 'pertimbangan_diberikan'])->count();
        $kasusSelesai = $cases->where('status', 'closed')->count();

        return view('admin.laporan-dispute', compact('cases', 'totalCases', 'kasusBaru', 'kasusProses', 'kasusSelesai'));
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

    public function platformAnalytics(\Illuminate\Http\Request $request)
    {
        $filter = $request->query('filter', 'all_time');
        
        $queryPesanan = \App\Models\Pesanan::query();
        $queryUser = \App\Models\User::where('role', '!=', 'admin');
        $queryPenyewa = \App\Models\User::where('role', 'penyewa');
        $queryPenyedia = \App\Models\User::where('role', 'penyedia');

        if ($filter === 'this_month') {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        } elseif ($filter === 'last_month') {
            $start = now()->subMonth()->startOfMonth();
            $end = now()->subMonth()->endOfMonth();
        } elseif ($filter === 'this_year') {
            $start = now()->startOfYear();
            $end = now()->endOfYear();
        } else { // all_time
            $start = null;
            $end = null;
        }

        if ($start && $end) {
            $queryPesanan->whereBetween('created_at', [$start, $end]);
            $queryUser->whereBetween('created_at', [$start, $end]);
            $queryPenyewa->whereBetween('created_at', [$start, $end]);
            $queryPenyedia->whereBetween('created_at', [$start, $end]);
        }

        $totalUsers = $queryUser->count();
        $totalTransaksi = (clone $queryPesanan)->count();
        $gmv = (clone $queryPesanan)->whereNotIn('status', ['cancelled', 'refunded', 'pending_payment'])->sum('total_biaya');
        $platformFee = $gmv * 0.1;

        $penyewaCount = $queryPenyewa->count();
        $penyediaCount = $queryPenyedia->count();
        $adminCount = \App\Models\User::where('role', 'admin')->count();

        // 1. Tren Pertumbuhan Transaksi (10 hari terakhir dari filter yang dipilih)
        $trenTransaksi = (clone $queryPesanan)->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();
            
        $trenLabels = $trenTransaksi->pluck('date')->map(function($d) { return \Carbon\Carbon::parse($d)->translatedFormat('d M'); })->toArray();
        $trenData = $trenTransaksi->pluck('total')->toArray();

        // 2. Kategori Terlaris
        $queryKategori = \Illuminate\Support\Facades\DB::table('pesanan_item')
            ->join('pesanan', 'pesanan_item.pesanan_id', '=', 'pesanan.id')
            ->join('barang', 'pesanan_item.barang_id', '=', 'barang.id')
            ->join('kategori', 'barang.kategori_id', '=', 'kategori.id')
            ->select('kategori.nama', \Illuminate\Support\Facades\DB::raw('COUNT(pesanan_item.id) as total_terjual'));
            
        if ($start && $end) {
            $queryKategori->whereBetween('pesanan.created_at', [$start, $end]);
        }
            
        $kategoriTerlaris = $queryKategori->groupBy('kategori.id', 'kategori.nama')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
            
        $totalTerjualAll = $kategoriTerlaris->sum('total_terjual');

        // 3. Status Transaksi Keseluruhan
        $statusTransaksi = (clone $queryPesanan)->select('status', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('admin.platform-analytics', compact(
            'totalUsers', 'totalTransaksi', 'gmv', 'platformFee', 
            'penyewaCount', 'penyediaCount', 'adminCount',
            'trenLabels', 'trenData',
            'kategoriTerlaris', 'totalTerjualAll',
            'statusTransaksi', 'filter'
        ));
    }
}
