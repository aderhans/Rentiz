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
            $searchTerm = strtolower($request->q);
            $query->whereRaw('LOWER(nama) LIKE ?', ['%' . $searchTerm . '%']);
        }

        if ($request->filled('kota')) {
            $query->where('kota', $request->kota);
        }

        if ($request->filled('kategori') && $request->kategori != 'semua') {
            $query->where('kategori_id', $request->kategori);
        }

        $sort = $request->get('sort', 'populer');
        if ($sort == 'murah') {
            $query->orderBy('harga_per_hari', 'asc');
        } elseif ($sort == 'mahal') {
            $query->orderBy('harga_per_hari', 'desc');
        } elseif ($sort == 'terbaru') {
            $query->orderBy('created_at', 'desc');
        } else {
            // populer (sementara pakai ID terkecil / urutan awal, atau random jika tidak ada tabel transaksi)
            // kita pakai created_at asc saja untuk dummy
            $query->orderBy('id', 'asc');
        }

        $items = $query->get();

        $itemIds = $items->pluck('id');
        
        $locks = \Illuminate\Support\Facades\DB::table('inventory_lock')
            ->join('pesanan_item', 'inventory_lock.pesanan_item_id', '=', 'pesanan_item.id')
            ->whereIn('inventory_lock.barang_id', $itemIds)
            ->where('inventory_lock.is_active', true)
            ->where('inventory_lock.expired_at', '>', now())
            ->select('inventory_lock.barang_id', 'pesanan_item.tanggal_mulai', 'pesanan_item.tanggal_selesai')
            ->get();

        $bookedDates = [];
        foreach ($locks as $lock) {
            $bookedDates[$lock->barang_id][] = [
                'from' => \Carbon\Carbon::parse($lock->tanggal_mulai)->format('Y-m-d'),
                'to' => \Carbon\Carbon::parse($lock->tanggal_selesai)->format('Y-m-d')
            ];
        }

        $cities = \App\Models\Barang::select('kota')->distinct()->whereNotNull('kota')->pluck('kota');

        return view('penyewa.cari-barang', [
            'items'       => $items,
            'cities'      => $cities,
            'query'       => $request->get('q', ''),
            'kategori'    => $request->get('kategori', 'semua'),
            'kota'        => $request->get('kota', ''),
            'bookedDates' => $bookedDates,
        ]);
    }

    /**
     * Halaman Penyewaan Aktif (dummy)
     */
    public function penyewaanAktif()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $items = \App\Models\PesananItem::with(['barang.fotos', 'pesanan.pemilik', 'checklistPengambilan'])
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemesan_id', $user->id);
            })
            ->whereIn('status', ['confirmed', 'active'])
            ->latest()
            ->get();

        return view('penyewa.penyewaan-aktif', compact('items'));
    }

    public function formChecklist($id)
    {
        $item = \App\Models\PesananItem::with('barang')->findOrFail($id);
        
        if ($item->status !== 'confirmed') {
            return redirect()->route('penyewa.penyewaan-aktif')->with('error', 'Status pesanan tidak valid untuk mengisi checklist saat ini.');
        }

        return view('penyewa.checklist', compact('item'));
    }

    public function simpanChecklist(Request $request, $id)
    {
        $request->validate([
            'fungsionalitas' => 'required|string',
            'kelengkapan' => 'required|string',
            'kondisi_fisik' => 'required|string',
            'foto_bukti' => 'required|image|max:2048',
            'catatan' => 'nullable|string'
        ]);

        $item = \App\Models\PesananItem::findOrFail($id);
        $user = \Illuminate\Support\Facades\Auth::user();

        // Hindari double submit
        if (\App\Models\ChecklistSerahTerima::where('pesanan_item_id', $item->id)->where('tipe', 'pengambilan')->exists()) {
            return redirect()->back()->with('error', 'Checklist sudah dikirim.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                $fotoPath = $request->file('foto_bukti')->store('checklists', 'public');
            }

            $kondisi = [
                'fungsionalitas' => $request->fungsionalitas,
                'kelengkapan' => $request->kelengkapan,
                'kondisi_fisik' => $request->kondisi_fisik,
            ];

            \App\Models\ChecklistSerahTerima::create([
                'pesanan_item_id' => $item->id,
                'diisi_oleh' => $user->id,
                'tipe' => 'pengambilan',
                'kondisi_barang' => $kondisi,
                'foto_bukti' => $fotoPath,
                'catatan' => $request->catatan,
                'status_verifikasi' => 'pending',
                'dicatat_at' => now(),
            ]);

            // Status TETAP 'confirmed' karena menunggu verifikasi penyedia.
            // Branch lain yang akan mengubahnya menjadi 'active' melalui fungsi verifikasi.

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('penyewa.penyewaan-aktif')->with('success', 'Checklist berhasil dikirim! Menunggu verifikasi dari penyedia.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        $user = \Illuminate\Support\Facades\Auth::user();

        $tagihans = \App\Models\Pembayaran::with(['pesanan.items.barang'])
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemesan_id', $user->id);
            })
            ->where('status', 'pending')
            ->latest()
            ->get();

        $riwayats = \App\Models\Pembayaran::with(['pesanan.items.barang'])
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemesan_id', $user->id);
            })
            ->whereIn('status', ['paid', 'failed', 'refunded', 'expired'])
            ->latest()
            ->get();

        return view('penyewa.pembayaran', compact('tagihans', 'riwayats'));
    }

    public function prosesBayar($id)
    {
        $pembayaran = \App\Models\Pembayaran::findOrFail($id);
        
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $pembayaran->update(['status' => 'paid', 'dibayar_at' => now()]);
            
            $pesanan = $pembayaran->pesanan;
            $pesanan->update(['status' => 'paid', 'payment_timestamp' => now()]);

            foreach ($pesanan->items as $item) {
                $item->update(['status' => 'confirmed']);
                // Perpanjang lock hingga akhir hari pada tanggal selesai
                if ($item->inventoryLock) {
                    $item->inventoryLock->update([
                        'expired_at' => \Carbon\Carbon::parse($item->tanggal_selesai)->endOfDay()
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil disimulasikan!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
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

        foreach ($keranjangs as $item) {
            $mulai = \Carbon\Carbon::parse($item->tanggal_mulai)->startOfDay();
            $selesai = \Carbon\Carbon::parse($item->tanggal_selesai)->startOfDay();
            
            $overlap = \Illuminate\Support\Facades\DB::table('inventory_lock')
                ->join('pesanan_item', 'inventory_lock.pesanan_item_id', '=', 'pesanan_item.id')
                ->where('inventory_lock.barang_id', $item->barang_id)
                ->where('inventory_lock.is_active', true)
                ->where('inventory_lock.expired_at', '>', now())
                ->where(function ($query) use ($mulai, $selesai) {
                    $query->whereBetween('pesanan_item.tanggal_mulai', [$mulai, $selesai])
                          ->orWhereBetween('pesanan_item.tanggal_selesai', [$mulai, $selesai])
                          ->orWhere(function($q) use ($mulai, $selesai) {
                              $q->where('pesanan_item.tanggal_mulai', '<=', $mulai)
                                ->where('pesanan_item.tanggal_selesai', '>=', $selesai);
                          });
                })->exists();

            $item->is_overlap = $overlap;
        }

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
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'action_type' => 'required|in:keranjang,sewa'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        $barang = \App\Models\Barang::findOrFail($request->barang_id);
        if ($barang->user_id == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menyewa atau memasukkan barang milik Anda sendiri ke keranjang.');
        }

        // Cek overlap tanggal (mencegah user by-pass UI disabled date)
        $mulai = \Carbon\Carbon::parse($request->tanggal_mulai)->startOfDay();
        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai)->startOfDay();
        
        $overlap = \Illuminate\Support\Facades\DB::table('inventory_lock')
            ->join('pesanan_item', 'inventory_lock.pesanan_item_id', '=', 'pesanan_item.id')
            ->where('inventory_lock.barang_id', $request->barang_id)
            ->where('inventory_lock.is_active', true)
            ->where('inventory_lock.expired_at', '>', now())
            ->where(function ($query) use ($mulai, $selesai) {
                $query->whereBetween('pesanan_item.tanggal_mulai', [$mulai, $selesai])
                      ->orWhereBetween('pesanan_item.tanggal_selesai', [$mulai, $selesai])
                      ->orWhere(function($q) use ($mulai, $selesai) {
                          $q->where('pesanan_item.tanggal_mulai', '<=', $mulai)
                            ->where('pesanan_item.tanggal_selesai', '>=', $selesai);
                      });
            })->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'Maaf, tanggal yang dipilih sudah dipesan/dikunci oleh pengguna lain.');
        }

        // Cek apakah barang sudah ada di keranjang
        $existing = \App\Models\Keranjang::where('user_id', $user->id)
            ->where('barang_id', $request->barang_id)
            ->first();

        if ($existing) {
            $existing->update([
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai
            ]);
            $keranjang = $existing;
        } else {
            $keranjang = \App\Models\Keranjang::create([
                'user_id' => $user->id,
                'barang_id' => $request->barang_id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai
            ]);
        }

        if ($request->action_type == 'sewa') {
            // Arahkan langsung ke halaman checkout
            return redirect()->route('penyewa.keranjang.checkout', ['keranjang_ids' => [$keranjang->id]]);
        }

        return redirect()->back()->with('success', 'Barang dan tanggal sewa berhasil disimpan ke keranjang!');
    }

    public function updateTanggalKeranjang(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $keranjang = \App\Models\Keranjang::where('id', $id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->firstOrFail();

        // Cek overlap tanggal
        $mulai = \Carbon\Carbon::parse($request->tanggal_mulai)->startOfDay();
        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai)->startOfDay();
        
        $overlap = \Illuminate\Support\Facades\DB::table('inventory_lock')
            ->join('pesanan_item', 'inventory_lock.pesanan_item_id', '=', 'pesanan_item.id')
            ->where('inventory_lock.barang_id', $keranjang->barang_id)
            ->where('inventory_lock.is_active', true)
            ->where('inventory_lock.expired_at', '>', now())
            ->where(function ($query) use ($mulai, $selesai) {
                $query->whereBetween('pesanan_item.tanggal_mulai', [$mulai, $selesai])
                      ->orWhereBetween('pesanan_item.tanggal_selesai', [$mulai, $selesai])
                      ->orWhere(function($q) use ($mulai, $selesai) {
                          $q->where('pesanan_item.tanggal_mulai', '<=', $mulai)
                            ->where('pesanan_item.tanggal_selesai', '>=', $selesai);
                      });
            })->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'Maaf, rentang tanggal tersebut sudah dipesan oleh pengguna lain.');
        }

        $keranjang->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->back()->with('success', 'Tanggal sewa berhasil diperbarui!');
    }

    public function hapusKeranjang($id)
    {
        $keranjang = \App\Models\Keranjang::where('id', $id)->where('user_id', \Illuminate\Support\Facades\Auth::id())->firstOrFail();
        $keranjang->delete();

        return redirect()->back()->with('success', 'Barang dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        $keranjangIds = $request->input('keranjang_ids');
        
        if (empty($keranjangIds)) {
            return redirect()->route('penyewa.keranjang')->with('info', 'Tidak ada barang yang dipilih untuk checkout.');
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        
        $items = \App\Models\Keranjang::with(['barang.user', 'barang.fotos'])
            ->whereIn('id', $keranjangIds)
            ->where('user_id', $user->id)
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('penyewa.keranjang')->with('error', 'Barang tidak valid.');
        }

        // Ambil penyedia dari item pertama (karena checkout per toko)
        $penyedia = $items->first()->barang->user;

        return view('penyewa.checkout', compact('items', 'penyedia'));
    }

    public function detailBarang($id)
    {
        $barang = \App\Models\Barang::with(['user', 'fotos'])->findOrFail($id);
        
        $locks = \Illuminate\Support\Facades\DB::table('inventory_lock')
            ->join('pesanan_item', 'inventory_lock.pesanan_item_id', '=', 'pesanan_item.id')
            ->where('inventory_lock.barang_id', $id)
            ->where('inventory_lock.is_active', true)
            ->where('inventory_lock.expired_at', '>', now())
            ->select('pesanan_item.tanggal_mulai', 'pesanan_item.tanggal_selesai')
            ->get();

        $bookedDates = [];
        foreach ($locks as $lock) {
            $bookedDates[] = [
                'from' => \Carbon\Carbon::parse($lock->tanggal_mulai)->format('Y-m-d'),
                'to' => \Carbon\Carbon::parse($lock->tanggal_selesai)->format('Y-m-d')
            ];
        }
        
        return view('penyewa.detail-barang', compact('barang', 'bookedDates'));
    }

    public function prosesCheckout(Request $request)
    {
        $request->validate([
            'keranjang_ids' => 'required|array',
            'keranjang_ids.*' => 'exists:keranjangs,id'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $keranjangs = \App\Models\Keranjang::with('barang')->whereIn('id', $request->keranjang_ids)->where('user_id', $user->id)->get();

        if ($keranjangs->isEmpty()) {
            return redirect()->back()->with('error', 'Pesanan tidak valid.');
        }

        $penyedia_id = $keranjangs->first()->barang->user_id;
        $totalSubtotal = 0;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Cek Inventory Lock overlap
            foreach ($keranjangs as $k) {
                $mulai = \Carbon\Carbon::parse($k->tanggal_mulai)->startOfDay();
                $selesai = \Carbon\Carbon::parse($k->tanggal_selesai)->startOfDay();
                
                // Cari apakah ada lock yang bersinggungan tanggalnya untuk barang ini
                $overlap = \Illuminate\Support\Facades\DB::table('inventory_lock')
                    ->join('pesanan_item', 'inventory_lock.pesanan_item_id', '=', 'pesanan_item.id')
                    ->where('inventory_lock.barang_id', $k->barang_id)
                    ->where('inventory_lock.is_active', true)
                    ->where('inventory_lock.expired_at', '>', now())
                    ->where(function ($query) use ($mulai, $selesai) {
                        $query->whereBetween('pesanan_item.tanggal_mulai', [$mulai, $selesai])
                              ->orWhereBetween('pesanan_item.tanggal_selesai', [$mulai, $selesai])
                              ->orWhere(function($q) use ($mulai, $selesai) {
                                  $q->where('pesanan_item.tanggal_mulai', '<=', $mulai)
                                    ->where('pesanan_item.tanggal_selesai', '>=', $selesai);
                              });
                    })->exists();

                if ($overlap) {
                    throw new \Exception("Maaf, barang '{$k->barang->nama}' sudah dipesan/dikunci pada rentang tanggal tersebut oleh orang lain.");
                }

                $durasi = $mulai->diffInDays($selesai);
                if ($durasi == 0) $durasi = 1;
                $totalSubtotal += ($k->barang->harga_per_hari * $durasi);
            }

            $biayaLayanan = 2000;
            $totalPembayaran = $totalSubtotal + $biayaLayanan;

            // 2. Buat Pesanan
            $pesanan = \App\Models\Pesanan::create([
                'pemesan_id' => $user->id,
                'pemilik_id' => $penyedia_id,
                'total_biaya' => $totalPembayaran,
                'status' => 'pending_payment'
            ]);

            // 3. Buat Items & Lock
            foreach ($keranjangs as $k) {
                $mulai = \Carbon\Carbon::parse($k->tanggal_mulai)->startOfDay();
                $selesai = \Carbon\Carbon::parse($k->tanggal_selesai)->startOfDay();
                $durasi = $mulai->diffInDays($selesai);
                if ($durasi == 0) $durasi = 1;
                $subtotal = $k->barang->harga_per_hari * $durasi;

                $item = \App\Models\PesananItem::create([
                    'pesanan_id' => $pesanan->id,
                    'barang_id' => $k->barang_id,
                    'tanggal_mulai' => $mulai,
                    'tanggal_selesai' => $selesai,
                    'durasi_hari' => $durasi,
                    'harga_per_hari' => $k->barang->harga_per_hari,
                    'subtotal' => $subtotal,
                    'denda' => 0,
                    'status' => 'pending'
                ]);

                \App\Models\InventoryLock::create([
                    'barang_id' => $k->barang_id,
                    'pesanan_item_id' => $item->id,
                    'user_id' => $user->id,
                    'expired_at' => now()->addHours(24),
                    'is_active' => true
                ]);
            }

            // 4. Buat Pembayaran
            $pembayaran = \App\Models\Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'jumlah' => $totalPembayaran,
                'status' => 'pending',
                'expired_at' => now()->addHours(24)
            ]);

            // 5. Buat Transaksi Escrow
            \App\Models\Transaksi::create([
                'pesanan_id' => $pesanan->id,
                'pembayaran_id' => $pembayaran->id,
                'status_escrow' => 'ditahan'
            ]);

            // 6. Hapus Keranjang
            \App\Models\Keranjang::whereIn('id', $keranjangs->pluck('id'))->delete();

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('penyewa.pembayaran')->with('success', 'Pesanan berhasil dibuat! Selesaikan pembayaran Anda sebelum ' . now()->addHours(24)->format('H:i'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
