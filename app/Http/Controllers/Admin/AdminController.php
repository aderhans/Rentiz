<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function manajemenUser(Request $request)
    {
        $query = User::where('role', '!=', 'admin')
            ->orderByRaw("CASE WHEN created_at >= NOW() - INTERVAL 7 DAY THEN 0 ELSE 1 END")
            ->orderByDesc('created_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'semua') {
            // role 'penyewa'/'penyedia' di DB disimpan sebagai 'user'
            // karena sistem pakai session mode, semua user biasa = 'user'
            // filter berdasarkan mode tidak applicable langsung
        }

        $users = $query->paginate(15)->withQueryString();

        $totalUser    = User::where('role', '!=', 'admin')->count();
        $userMingguIni = User::where('role', '!=', 'admin')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $suspended    = User::where('role', 'suspended')->count();

        return view('admin.manajemen-user', compact(
            'users', 'totalUser', 'userMingguIni', 'suspended'
        ));
    }

    public function suspendUser(Request $request, $id)
    {
        $user = User::where('id', $id)->where('role', '!=', 'admin')->firstOrFail();
        $user->update(['role' => 'suspended']);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'suspended', 'message' => 'Akun berhasil di-suspend.']);
        }
        return redirect()->back()->with('success', "Akun {$user->name} berhasil di-suspend.");
    }

    public function activateUser(Request $request, $id)
    {
        $user = User::where('id', $id)->where('role', 'suspended')->firstOrFail();
        $user->update(['role' => 'user']);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'aktif', 'message' => 'Akun berhasil diaktifkan.']);
        }
        return redirect()->back()->with('success', "Akun {$user->name} berhasil diaktifkan.");
    }

    public function semuaListing()
    {
        $items = \App\Models\Barang::with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->get();
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

    public function semuaTransaksi(Request $request)
    {
        $query = DB::table('pesanan as p')
            ->join('users as u', 'p.pemesan_id', '=', 'u.id')
            ->leftJoin('pesanan_item as pi', 'p.id', '=', 'pi.pesanan_id')
            ->leftJoin('barang as b', 'pi.barang_id', '=', 'b.id')
            ->select(
                'p.id', 'p.status', 'p.total_biaya', 'p.created_at',
                'u.name as pemesan_name', 'u.email as pemesan_email',
                DB::raw('GROUP_CONCAT(b.nama SEPARATOR ", ") as barang_names')
            )
            ->groupBy('p.id', 'p.status', 'p.total_biaya', 'p.created_at', 'u.name', 'u.email')
            ->orderByDesc('p.created_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('u.name', 'like', "%{$q}%")
                    ->orWhere('u.email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('p.status', $request->status);
        }

        $pesanan = $query->paginate(20)->withQueryString();

        $totalTransaksi  = DB::table('pesanan')->count();
        $totalPendapatan = (float) DB::table('pesanan')
            ->whereNotIn('status', ['cancelled', 'refunded'])->sum('total_biaya');
        $bulanIni = (float) DB::table('pesanan')
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_biaya');
        $pending = DB::table('pesanan')->where('status', 'pending_payment')->count();

        return view('admin.semua-transaksi', compact(
            'pesanan', 'totalTransaksi', 'totalPendapatan', 'bulanIni', 'pending'
        ));
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
        $now = now();

        $totalUser     = User::where('role', '!=', 'admin')->count();
        $totalBarang   = DB::table('barang')->where('status', 'active')->count();
        $totalPesanan  = DB::table('pesanan')->count();
        $totalRevenue  = (float) DB::table('pesanan')
            ->whereNotIn('status', ['cancelled', 'refunded'])->sum('total_biaya');

        // Monthly revenue last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $val  = (float) DB::table('pesanan')
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_biaya');
            $monthlyRevenue[] = ['label' => $date->format('M'), 'value' => $val];
        }
        $maxRevenue = max(array_column($monthlyRevenue, 'value')) ?: 1;

        return view('admin.platform-analytics', compact(
            'totalUser', 'totalBarang', 'totalPesanan', 'totalRevenue',
            'monthlyRevenue', 'maxRevenue'
        ));
    }
}
