<?php

namespace App\Http\Controllers\Penyedia;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenyediaController extends Controller
{
    public function daftarBarang()
    {
        $items = Barang::with('fotos')
            ->leftJoin('kategori as k', 'barang.kategori_id', '=', 'k.id')
            ->where('barang.user_id', auth()->id())
            ->select('barang.*', 'k.nama as kategori_nama')
            ->get();
        $itemIds = $items->pluck('id')->all();

        $categories = DB::table('kategori')
            ->orderBy('nama')
            ->get();

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
            'categories',
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
        $categories = DB::table('kategori')
            ->orderBy('nama')
            ->get();

        return view('penyedia.tambah-barang', compact('categories'));
    }

    public function storeBarang(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|uuid',
            'city' => 'required|string',
            'alamat_pengambilan' => 'required|string',
            'ketentuan_jaminan' => 'nullable|string',
            'kondisi' => 'required|string',
            'min_durasi_sewa' => 'required|integer',
            'max_durasi_sewa' => 'required|integer',
            'images' => 'required|array|min:1|max:4',
            'images.*' => 'image|max:2048' // max 2MB per image
        ]);

        $barang = \App\Models\Barang::create([
            'user_id' => auth()->id(),
            'kategori_id' => $validated['category'],
            'nama' => $validated['title'],
            'deskripsi' => $validated['description'],
            'kondisi' => $validated['kondisi'],
            'harga_per_hari' => $validated['price'],
            'kota' => $validated['city'],
            'alamat_pengambilan' => $validated['alamat_pengambilan'],
            'ketentuan_jaminan' => $validated['ketentuan_jaminan'] ?? null,
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

    public function updateBarang(Request $request, $id)
    {
        $barang = Barang::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|uuid',
            'condition' => 'required|string',
            'min_durasi_sewa' => 'required|integer',
            'max_durasi_sewa' => 'required|integer',
            'city' => 'required|string',
            'alamat_pengambilan' => 'nullable|string',
            'ketentuan_jaminan' => 'nullable|string',
        ]);

        $barang->update([
            'nama' => $validated['title'],
            'deskripsi' => $validated['description'],
            'harga_per_hari' => $validated['price'],
            'kategori_id' => $validated['category'],
            'kondisi' => $validated['condition'],
            'kota' => $validated['city'],
            'alamat_pengambilan' => $validated['alamat_pengambilan'] ?? null,
            'ketentuan_jaminan' => $validated['ketentuan_jaminan'] ?? null,
            'min_durasi_sewa' => $validated['min_durasi_sewa'],
            'max_durasi_sewa' => $validated['max_durasi_sewa'],
            'status' => 'pending',
        ]);

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            if (is_array($images) && count($images) > 0) {
                \App\Models\FotoBarang::where('barang_id', $barang->id)->delete();
                foreach ($images as $index => $image) {
                    $path = $image->store('public/items');
                    \App\Models\FotoBarang::create([
                        'barang_id' => $barang->id,
                        'path_foto' => str_replace('public/', '', $path),
                        'is_primary' => $index === 0,
                    ]);
                }
            }
        }

        return redirect()->route('penyedia.daftar-barang')->with('success', 'Barang berhasil diperbarui dan menunggu verifikasi admin.');
    }

    public function destroy($id)
    {
        $barang = Barang::where('user_id', auth()->id())->findOrFail($id);
        \App\Models\FotoBarang::where('barang_id', $barang->id)->delete();
        $barang->delete();

        return redirect()->route('penyedia.daftar-barang')->with('success', 'Barang berhasil dihapus.');
    }

    public function requestSewa()
    {
        $user = auth()->user();

        // 1. Sinkronisasi notifikasi untuk pesanan yang sudah dibayar jika belum tercatat
        $paidOrders = \App\Models\Pesanan::with(['items.barang', 'pemesan', 'pembayaran'])
            ->where('pemilik_id', $user->id)
            ->where('status', 'paid')
            ->get();

        foreach ($paidOrders as $ord) {
            $hasNotif = \App\Models\Notifikasi::where('user_id', $user->id)
                ->where('pesanan_id', $ord->id)
                ->where('tipe', 'pesanan_masuk')
                ->exists();

            if (!$hasNotif) {
                $bName = $ord->items->first() && $ord->items->first()->barang ? $ord->items->first()->barang->nama : 'Barang';
                $pName = $ord->pemesan ? $ord->pemesan->name : 'Penyewa';
                $jumlahBayar = $ord->pembayaran ? $ord->pembayaran->jumlah : $ord->total_biaya;

                \App\Models\Notifikasi::create([
                    'user_id' => $user->id,
                    'pesanan_id' => $ord->id,
                    'untuk_role' => 'penyedia',
                    'tipe' => 'pesanan_masuk',
                    'judul' => '📦 Pesanan Baru Masuk!',
                    'pesan' => 'Barang "' . $bName . '" telah disewa oleh ' . $pName . '. Pembayaran telah lunas (Rp ' . number_format($jumlahBayar, 0, ',', '.') . '). Silakan siapkan barang untuk serah terima.',
                    'channel' => 'in_app',
                    'is_read' => false,
                    'dikirim_at' => $ord->payment_timestamp ?? $ord->updated_at ?? now(),
                ]);
            }
        }

        // 2. Query items pesanan sewa milik penyedia (semua status kecuali cancelled)
        $items = \App\Models\PesananItem::with(['barang.fotos', 'pesanan.pemesan', 'checklistPengambilan'])
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemilik_id', $user->id)
                  ->whereIn('status', ['paid', 'confirmed', 'active', 'completed']);
            })
            ->latest()
            ->get();

        $totalSemua    = $items->count();
        $totalMenunggu = $items->where('status', 'confirmed')->count();
        $totalAktif    = $items->where('status', 'active')->count();
        $totalSelesai  = $items->whereIn('status', ['completed', 'returned'])->count();

        return view('penyedia.request-sewa', compact(
            'items',
            'totalSemua',
            'totalMenunggu',
            'totalAktif',
            'totalSelesai'
        ));
    }

    /**
     * Form Checklist Pengembalian oleh Penyedia
     * Diisi ketika penyewa mengembalikan barang → trigger status 'completed'
     */
    public function formChecklistPengembalian($id)
    {
        $user = auth()->user();
        $item = \App\Models\PesananItem::with(['barang.fotos', 'pesanan.pemesan'])
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemilik_id', $user->id);
            })
            ->findOrFail($id);

        // Hanya bisa diisi saat barang sedang aktif disewa (sudah diambil penyewa)
        if ($item->status !== 'active') {
            return redirect()->route('penyedia.request-sewa')
                ->with('error', 'Checklist pengembalian hanya bisa diisi untuk barang yang sedang aktif disewa.');
        }

        // Cek apakah checklist pengembalian sudah pernah diisi
        $existingChecklist = \App\Models\ChecklistSerahTerima::where('pesanan_item_id', $item->id)
            ->where('tipe', 'pengembalian')
            ->first();

        // Ambil juga checklist pengambilan (diisi penyewa saat mengambil barang) untuk referensi kondisi awal
        $checklistPengambilan = \App\Models\ChecklistSerahTerima::where('pesanan_item_id', $item->id)
            ->where('tipe', 'pengambilan')
            ->first();

        return view('penyedia.checklist-pengembalian', compact('item', 'existingChecklist', 'checklistPengambilan'));
    }

    /**
     * Simpan Checklist Pengembalian → trigger status 'completed'
     * Penyedia verifikasi kondisi barang yang dikembalikan penyewa
     */
    public function simpanChecklistPengembalian(Request $request, $id)
    {
        $request->validate([
            'fungsionalitas'  => 'required|string',
            'kelengkapan'     => 'required|string',
            'kondisi_fisik'   => 'required|string',
            'foto_bukti'      => 'nullable|image|max:3072',
            'catatan'         => 'nullable|string|max:1000',
        ], [
            'fungsionalitas.required' => 'Kondisi fungsionalitas wajib dipilih.',
            'kelengkapan.required'    => 'Kelengkapan barang wajib dipilih.',
            'kondisi_fisik.required'  => 'Kondisi fisik barang wajib dipilih.',
        ]);

        $user = auth()->user();
        $item = \App\Models\PesananItem::with(['barang', 'pesanan'])
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemilik_id', $user->id);
            })
            ->findOrFail($id);

        // Cegah double submit
        if (\App\Models\ChecklistSerahTerima::where('pesanan_item_id', $item->id)
                ->where('tipe', 'pengembalian')->exists()) {
            return redirect()->route('penyedia.request-sewa')
                ->with('error', 'Checklist pengembalian untuk barang ini sudah pernah diisi.');
        }

        try {
            DB::beginTransaction();

            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                $fotoPath = $request->file('foto_bukti')->store('checklists', 'public');
            }

            $kondisi = [
                'fungsionalitas' => $request->fungsionalitas,
                'kelengkapan'    => $request->kelengkapan,
                'kondisi_fisik'  => $request->kondisi_fisik,
            ];

            // 1. Simpan checklist pengembalian yang diisi penyedia
            \App\Models\ChecklistSerahTerima::create([
                'pesanan_item_id'   => $item->id,
                'diisi_oleh'        => $user->id,
                'tipe'              => 'pengembalian',
                'kondisi_barang'    => $kondisi,
                'foto_bukti'        => $fotoPath,
                'catatan'           => $request->catatan,
                'status_verifikasi' => 'verified',
                'dicatat_at'        => now(),
            ]);

            // 2. Status item → 'completed' (sewa selesai)
            $item->update(['status' => 'completed']);

            // 3. Cek apakah semua item dalam pesanan sudah selesai
            $pesanan = $item->pesanan;
            $allDone = $pesanan->items()->whereNotIn('status', ['completed', 'returned'])->doesntExist();
            if ($allDone) {
                $pesanan->update(['status' => 'completed']);
            }

            // 4. Notifikasi ke Penyewa: transaksi sewa selesai
            \App\Models\Notifikasi::create([
                'user_id'    => $pesanan->pemesan_id,
                'pesanan_id' => $pesanan->id,
                'untuk_role' => 'penyewa',
                'tipe'       => 'pesanan_selesai',
                'judul'      => '🎊 Sewa Selesai!',
                'pesan'      => 'Penyedia telah memverifikasi pengembalian barang "' . ($item->barang->nama ?? 'Barang') . '". Transaksi sewa Anda dinyatakan selesai. Terima kasih!',
                'channel'    => 'in_app',
                'is_read'    => false,
                'dikirim_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('penyedia.request-sewa')
                ->with('success', '🎊 Pengembalian barang "' . ($item->barang->nama ?? '') . '" berhasil dikonfirmasi! Transaksi sewa dinyatakan selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan checklist pengembalian: ' . $e->getMessage());
        }
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
        // Pastikan tabel penarikan_dana otomatis tersedia
        if (!\Illuminate\Support\Facades\Schema::hasTable('penarikan_dana')) {
            \Illuminate\Support\Facades\Schema::create('penarikan_dana', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id');
                $table->decimal('jumlah', 12, 2);
                $table->decimal('biaya_admin', 12, 2)->default(2500);
                $table->decimal('jumlah_diterima', 12, 2);
                $table->string('bank_tujuan')->default('BCA');
                $table->string('nomor_rekening');
                $table->string('atas_nama');
                $table->string('status')->default('selesai');
                $table->timestamps();

                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');

                $table->index('user_id');
                $table->index('status');
                $table->index('created_at');
            });
        }

        $userId = auth()->id();

        // Ambil semua pesanan milik penyedia ini yang telah dibayar oleh penyewa
        $paidOrders = \App\Models\Pesanan::with(['pemesan', 'items.barang', 'pembayaran'])
            ->where('pemilik_id', $userId)
            ->where(function ($q) {
                $q->whereIn('status', ['paid', 'confirmed', 'active', 'returned', 'completed'])
                  ->orWhereHas('pembayaran', function ($pq) {
                      $pq->where('status', 'paid');
                  });
            })
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung total pendapatan utuh biaya sewa (di luar biaya layanan admin Rp 2.000)
        $totalPendapatan = (float) $paidOrders->sum(function ($order) {
            $itemsSum = $order->items->sum('subtotal');
            return $itemsSum > 0 ? (float) $itemsSum : max(0, (float) $order->total_biaya - 2000);
        });

        // Pesanan yang masih pending_payment (sedang dibooking / dalam proses bayar)
        $pendingOrders = \App\Models\Pesanan::with(['items'])
            ->where('pemilik_id', $userId)
            ->where('status', 'pending_payment')
            ->get();

        $dalamProses = (float) $pendingOrders->sum(function ($order) {
            $itemsSum = $order->items->sum('subtotal');
            return $itemsSum > 0 ? (float) $itemsSum : max(0, (float) $order->total_biaya - 2000);
        });

        // Riwayat penarikan dana yang pernah dilakukan penyedia
        $riwayatPenarikan = \App\Models\PenarikanDana::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalDitarik = (float) $riwayatPenarikan->whereIn('status', ['selesai', 'diproses'])->sum('jumlah');

        // Saldo yang siap ditarik (Instant Model: Total Pendapatan Masuk dikurangi Total yang sudah ditarik)
        $saldoTersedia = max(0, $totalPendapatan - $totalDitarik);
        $saldoTotal = $totalPendapatan;

        return view('penyedia.penarikan-dana', compact(
            'paidOrders',
            'riwayatPenarikan',
            'saldoTersedia',
            'saldoTotal',
            'dalamProses',
            'totalDitarik'
        ));
    }

    public function prosesPenarikanDana(Request $request)
    {
        $userId = auth()->id();

        $request->validate([
            'jumlah' => 'required|numeric|min:10000',
            'bank_tujuan' => 'required|string',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:100',
        ], [
            'jumlah.required' => 'Nominal penarikan wajib diisi.',
            'jumlah.numeric' => 'Nominal penarikan harus berupa angka.',
            'jumlah.min' => 'Minimum penarikan adalah Rp 10.000.',
            'bank_tujuan.required' => 'Pilih bank atau e-wallet tujuan.',
            'nomor_rekening.required' => 'Nomor rekening atau e-wallet wajib diisi.',
            'atas_nama.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        // Verifikasi saldo tersedia secara server-side
        $paidOrders = \App\Models\Pesanan::with(['items'])
            ->where('pemilik_id', $userId)
            ->where(function ($q) {
                $q->whereIn('status', ['paid', 'confirmed', 'active', 'returned', 'completed'])
                  ->orWhereHas('pembayaran', function ($pq) {
                      $pq->where('status', 'paid');
                  });
            })
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->get();

        $totalPendapatan = (float) $paidOrders->sum(function ($order) {
            $itemsSum = $order->items->sum('subtotal');
            return $itemsSum > 0 ? (float) $itemsSum : max(0, (float) $order->total_biaya - 2000);
        });

        $totalDitarik = (float) \App\Models\PenarikanDana::where('user_id', $userId)
            ->whereIn('status', ['selesai', 'diproses'])
            ->sum('jumlah');

        $saldoTersedia = max(0, $totalPendapatan - $totalDitarik);
        $jumlahTarik = (float) $request->input('jumlah');

        if ($jumlahTarik > $saldoTersedia) {
            return back()->with('error', 'Saldo tidak mencukupi! Saldo tersedia Anda saat ini adalah Rp ' . number_format($saldoTersedia, 0, ',', '.'));
        }

        $biayaAdmin = 2500;
        if ($jumlahTarik <= $biayaAdmin) {
            return back()->with('error', 'Nominal penarikan harus lebih besar dari biaya transfer admin (Rp 2.500).');
        }
        $jumlahDiterima = $jumlahTarik - $biayaAdmin;

        DB::beginTransaction();
        try {
            \App\Models\PenarikanDana::create([
                'user_id' => $userId,
                'jumlah' => $jumlahTarik,
                'biaya_admin' => $biayaAdmin,
                'jumlah_diterima' => $jumlahDiterima,
                'bank_tujuan' => $request->input('bank_tujuan'),
                'nomor_rekening' => $request->input('nomor_rekening'),
                'atas_nama' => $request->input('atas_nama'),
                'status' => 'selesai',
            ]);

            // Catat ke log aktivitas sistem
            DB::table('log')->insert([
                'id' => (string) Str::uuid(),
                'user_id' => $userId,
                'aksi' => 'Penarikan Dana Berhasil',
                'detail' => 'Penarikan Rp ' . number_format($jumlahTarik, 0, ',', '.') . ' ke ' . strtoupper($request->bank_tujuan) . ' (' . $request->nomor_rekening . ' a.n. ' . $request->atas_nama . ') berhasil diproses.',
                'ip_address' => $request->ip(),
                'terjadi_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Penarikan dana Rp ' . number_format($jumlahTarik, 0, ',', '.') . ' berhasil diproses ke ' . strtoupper($request->bank_tujuan) . ' (' . $request->nomor_rekening . ')!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses penarikan dana: ' . $e->getMessage());
        }
    }

    public function profilToko()
    {
        return view('penyedia.profil-toko');
    }
}
