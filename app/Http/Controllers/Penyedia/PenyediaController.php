<?php

namespace App\Http\Controllers\Penyedia;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyediaController extends Controller
{
    public function daftarBarang()
    {
        $items = Barang::with('fotos')->where('user_id', auth()->id())->get();
        $itemIds = $items->pluck('id')->all();

        $rentedItemIds = [];
        if (!empty($itemIds)) {
            $rentedItemIds = DB::table('pesanan_item')
                ->whereIn('barang_id', $itemIds)
                ->whereIn('status', ['active', 'overdue'])
                ->pluck('barang_id')
                ->unique()
                ->all();
        }

        $totalListing = $items->count();
        $activeListing = $items->where('status', 'active')->count() - count($rentedItemIds);
        $rentedListing = count($rentedItemIds);
        $inactiveListing = $totalListing - $activeListing - $rentedListing;
        $pendapatanBulanIni = (float) DB::table('pesanan')
            ->where('pemilik_id', auth()->id())
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_biaya');

        return view('penyedia.daftar-barang', compact(
            'items',
            'totalListing',
            'activeListing',
            'rentedListing',
            'inactiveListing',
            'pendapatanBulanIni',
            'rentedItemIds'
        ));
    }

    public function tambahBarang()
    {
        return view('penyedia.tambah-barang');
    }

    public function storeBarang(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'city' => 'required|string',
            'kondisi' => 'required|string',
            'min_durasi_sewa' => 'required|integer',
            'max_durasi_sewa' => 'required|integer',
            'images' => 'required|array|min:1|max:4',
            'images.*' => 'image|max:2048' // max 2MB per image
        ]);

        $barang = \App\Models\Barang::create([
            'user_id' => auth()->id(),
            'kategori_id' => null, // Kategori text bisa disimpan di relasi lain nanti, atau biarkan null sesuai plan
            'nama' => $validated['title'],
            'deskripsi' => $validated['description'],
            'kondisi' => $validated['kondisi'],
            'harga_per_hari' => $validated['price'],
            'kota' => $validated['city'],
            'min_durasi_sewa' => $validated['min_durasi_sewa'],
            'max_durasi_sewa' => $validated['max_durasi_sewa'],
            'status' => 'pending'
        ]);

        $images = $request->file('images');
        foreach ($images as $index => $image) {
            $path = $image->store('public/items');
            \App\Models\FotoBarang::create([
                'barang_id' => $barang->id,
                'path_foto' => str_replace('public/', '', $path),
                'is_primary' => $index === 0
            ]);
        }

        return redirect()->route('penyedia.daftar-barang')->with('success', 'Barang berhasil diunggah dan sedang menunggu verifikasi admin.');
    }

    public function requestSewa()
    {
        return view('penyedia.request-sewa');
    }

    public function riwayatTransaksi()
    {
        return view('penyedia.riwayat-transaksi');
    }

    public function analitik()
    {
        $userId = auth()->id();
        $now = now();
        $lastMonth = $now->copy()->subMonth();

        $revenueThisMonth = (float) DB::table('pesanan')
            ->where('pemilik_id', $userId)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('total_biaya');

        $revenueLastMonth = (float) DB::table('pesanan')
            ->where('pemilik_id', $userId)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->sum('total_biaya');

        if ($revenueLastMonth > 0) {
            $percent = round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100);
            $revenueChangeLabel = ($percent > 0 ? '+' : '') . $percent . '% vs bulan lalu';
            $revenueChangeClass = $percent >= 0 ? 'up' : 'down';
        } elseif ($revenueThisMonth > 0) {
            $revenueChangeLabel = 'Pendapatan baru bulan ini';
            $revenueChangeClass = 'up';
        } else {
            $revenueChangeLabel = 'Belum ada pendapatan';
            $revenueChangeClass = 'down';
        }

        $completedRentals = DB::table('pesanan_item as pi')
            ->join('pesanan as p', 'pi.pesanan_id', '=', 'p.id')
            ->where('p.pemilik_id', $userId)
            ->whereIn('pi.status', ['completed', 'returned'])
            ->count();

        $completedLastMonth = DB::table('pesanan_item as pi')
            ->join('pesanan as p', 'pi.pesanan_id', '=', 'p.id')
            ->where('p.pemilik_id', $userId)
            ->whereIn('pi.status', ['completed', 'returned'])
            ->whereYear('pi.created_at', $lastMonth->year)
            ->whereMonth('pi.created_at', $lastMonth->month)
            ->count();

        if ($completedLastMonth > 0) {
            $completedDiff = $completedRentals - $completedLastMonth;
            $completedChangeLabel = ($completedDiff >= 0 ? '+' : '') . $completedDiff . ' vs bulan lalu';
            $completedChangeClass = $completedDiff >= 0 ? 'up' : 'down';
        } elseif ($completedRentals > 0) {
            $completedChangeLabel = 'Baru bulan ini';
            $completedChangeClass = 'up';
        } else {
            $completedChangeLabel = 'Belum ada sewa selesai';
            $completedChangeClass = 'down';
        }

        $avgRating = DB::table('rating')
            ->join('barang', 'rating.barang_id', '=', 'barang.id')
            ->where('barang.user_id', $userId)
            ->avg('nilai');

        $averageRating = $avgRating ? round($avgRating, 1) : 0;

        $monthlyRevenueRaw = DB::table('pesanan')
            ->where('pemilik_id', $userId)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereBetween('created_at', [$now->copy()->subMonths(5)->startOfMonth(), $now->endOfMonth()])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_biaya) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($row) {
                return [sprintf('%04d-%02d', $row->year, $row->month) => (float) $row->total];
            });

        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $key = $date->format('Y-m');
            $value = $monthlyRevenueRaw->get($key, 0);
            $monthlyRevenue[] = [
                'label' => $date->format('M'),
                'value' => $value,
                'formatted' => 'Rp ' . number_format($value, 0, ',', '.'),
            ];
        }

        $maxRevenue = max(array_column($monthlyRevenue, 'value')) ?: 1;

        $categoryDistribution = DB::table('pesanan_item as pi')
            ->join('pesanan as p', 'pi.pesanan_id', '=', 'p.id')
            ->join('barang as b', 'pi.barang_id', '=', 'b.id')
            ->leftJoin('kategori as k', 'b.kategori_id', '=', 'k.id')
            ->where('p.pemilik_id', $userId)
            ->whereIn('pi.status', ['completed', 'returned'])
            ->selectRaw('COALESCE(k.nama, ? ) as kategori, COUNT(pi.id) as total', ['Lainnya'])
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        $categoryCount = $categoryDistribution->sum('total') ?: 1;
        $categoryDistribution = $categoryDistribution->map(function ($row) use ($categoryCount) {
            return [
                'label' => $row->kategori,
                'count' => $row->total,
                'percent' => round($row->total / $categoryCount * 100),
            ];
        })->toArray();

        $topItems = DB::table('pesanan_item as pi')
            ->join('pesanan as p', 'pi.pesanan_id', '=', 'p.id')
            ->join('barang as b', 'pi.barang_id', '=', 'b.id')
            ->leftJoin('kategori as k', 'b.kategori_id', '=', 'k.id')
            ->where('p.pemilik_id', $userId)
            ->whereIn('pi.status', ['completed', 'returned'])
            ->select('b.id', 'b.nama', 'k.nama as kategori', DB::raw('COUNT(pi.id) as total_rentals'), DB::raw('SUM(pi.subtotal) as revenue'))
            ->groupBy('b.id', 'b.nama', 'k.nama')
            ->orderByDesc('total_rentals')
            ->limit(5)
            ->get();

        return view('penyedia.analitik', compact(
            'revenueThisMonth',
            'revenueChangeLabel',
            'revenueChangeClass',
            'completedRentals',
            'completedChangeLabel',
            'completedChangeClass',
            'monthlyRevenue',
            'maxRevenue',
            'categoryDistribution',
            'averageRating',
            'topItems'
        ));
    }

    public function penarikanDana()
    {
        return view('penyedia.penarikan-dana');
    }

    public function profilToko()
    {
        return view('penyedia.profil-toko');
    }
}
