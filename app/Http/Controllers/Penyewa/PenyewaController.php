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
        $this->cleanupExpiredOrders();

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

        $user_wishlist = auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->pluck('barang_id')->toArray() : [];

        return view('penyewa.cari-barang', [
            'items'         => $items,
            'cities'        => $cities,
            'query'         => $request->get('q', ''),
            'kategori'      => $request->get('kategori', 'semua'),
            'kota'          => $request->get('kota', ''),
            'user_wishlist' => $user_wishlist,
            'bookedDates'   => $bookedDates,
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
            'kelengkapan'    => 'required|string',
            'kondisi_fisik'  => 'required|string',
            'foto_bukti'     => 'required|image|max:2048',
            'catatan'        => 'nullable|string'
        ]);

        $item = \App\Models\PesananItem::with(['barang', 'pesanan'])->findOrFail($id);
        $user = \Illuminate\Support\Facades\Auth::user();

        // Pastikan item milik penyewa ini
        if ($item->pesanan->pemesan_id !== $user->id) {
            return redirect()->route('penyewa.penyewaan-aktif')->with('error', 'Akses tidak diizinkan.');
        }

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
                'kelengkapan'    => $request->kelengkapan,
                'kondisi_fisik'  => $request->kondisi_fisik,
            ];

            // 1. Simpan checklist pengambilan (diisi penyewa)
            \App\Models\ChecklistSerahTerima::create([
                'pesanan_item_id'   => $item->id,
                'diisi_oleh'        => $user->id,
                'tipe'              => 'pengambilan',
                'kondisi_barang'    => $kondisi,
                'foto_bukti'        => $fotoPath,
                'catatan'           => $request->catatan,
                'status_verifikasi' => 'verified',
                'dicatat_at'        => now(),
            ]);

            // 2. Item status → 'active' (sewa aktif)
            $item->update(['status' => 'active']);

            // 3. Jika semua item dalam pesanan aktif, update pesanan induk juga
            $pesanan = $item->pesanan;
            $allActive = $pesanan->items()->where('status', '!=', 'active')->doesntExist();
            if ($allActive) {
                $pesanan->update(['status' => 'active']);
            }

            // 4. Notifikasi ke penyedia: barang telah diambil penyewa
            \App\Models\Notifikasi::create([
                'user_id'    => $pesanan->pemilik_id,
                'pesanan_id' => $pesanan->id,
                'untuk_role' => 'penyedia',
                'tipe'       => 'pesanan_masuk',
                'judul'      => '✅ Barang Telah Diambil!',
                'pesan'      => 'Penyewa telah mengambil dan mengisi checklist untuk barang "' . ($item->barang->nama ?? 'Barang') . '". Masa sewa sekarang aktif. Silakan konfirmasi pengembalian saat barang dikembalikan.',
                'channel'    => 'in_app',
                'is_read'    => false,
                'dikirim_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('penyewa.penyewaan-aktif')->with('success', '✅ Checklist berhasil dikirim! Masa sewa aktif telah dimulai.');
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
     * Halaman Wishlist
     */
    public function wishlist()
    {
        $wishlists = \App\Models\Wishlist::with(['barang.user', 'barang.fotos'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
            
        return view('penyewa.wishlist', compact('wishlists'));
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id'
        ]);

        $user_id = auth()->id();
        $barang_id = $request->barang_id;

        $wishlist = \App\Models\Wishlist::where('user_id', $user_id)->where('barang_id', $barang_id)->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'removed', 'message' => 'Dihapus dari wishlist']);
        } else {
            \App\Models\Wishlist::create([
                'user_id' => $user_id,
                'barang_id' => $barang_id,
            ]);
            return response()->json(['status' => 'added', 'message' => 'Ditambahkan ke wishlist']);
        }
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
     * Membersihkan tagihan dan lock inventaris yang sudah melewati batas waktu (10 menit).
     * Dijalankan secara otomatis (Lazy Expiration) untuk memastikan ketersediaan tanggal akurat secara real-time.
     */
    private function cleanupExpiredOrders()
    {
        $expiredPembayarans = \App\Models\Pembayaran::where('status', 'pending')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())
            ->with(['pesanan.items.inventoryLock'])
            ->get();

        foreach ($expiredPembayarans as $pembayaran) {
            $pembayaran->update(['status' => 'expired']);

            if ($pembayaran->pesanan) {
                $pembayaran->pesanan->update(['status' => 'cancelled']);

                foreach ($pembayaran->pesanan->items as $item) {
                    $item->update(['status' => 'cancelled']);
                    if ($item->inventoryLock) {
                        $item->inventoryLock->update(['is_active' => false]);
                    }
                }
            }
        }

        // Lepaskan juga inventory_lock yang berdiri sendiri jika sudah lewat expired_at
        \App\Models\InventoryLock::where('is_active', true)
            ->where('expired_at', '<=', now())
            ->update(['is_active' => false]);
    }

    /**
     * Halaman Pembayaran
     */
    public function pembayaran()
    {
        $this->cleanupExpiredOrders();

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

        $totalBulanIni = $riwayats
            ->where('status', 'paid')
            ->where('updated_at', '>=', now()->startOfMonth())
            ->sum('jumlah');

        return view('penyewa.pembayaran', compact('tagihans', 'riwayats', 'totalBulanIni'));
    }

    /**
     * Halaman Payment Gateway Simulasi (mirip Midtrans Snap)
     */
    public function showPaymentGateway($id)
    {
        $this->cleanupExpiredOrders();

        $user = \Illuminate\Support\Facades\Auth::user();
        $pembayaran = \App\Models\Pembayaran::with(['pesanan.items.barang'])
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemesan_id', $user->id);
            })
            ->where('id', $id)
            ->first();

        // 1. Tagihan tidak ditemukan
        if (!$pembayaran) {
            return redirect()->route('penyewa.pembayaran')->with('error', 'Data tagihan pembayaran tidak ditemukan.');
        }

        // 2. Tagihan sudah lunas dibayar (cegah 404 jika halaman di-refresh)
        if ($pembayaran->status === 'paid') {
            return redirect()->route('penyewa.penyewaan-aktif')->with('info', 'Pembayaran untuk pesanan ini telah selesai. Anda dapat memantau status barang di menu Penyewaan Aktif.');
        }

        // 3. Status tidak pending (dibatalkan / kadaluwarsa)
        if ($pembayaran->status !== 'pending') {
            return redirect()->route('penyewa.pembayaran')->with('error', 'Tagihan pembayaran ini sudah tidak berlaku atau telah dibatalkan.');
        }

        // 4. Cek batas waktu kadaluwarsa
        if ($pembayaran->expired_at && \Carbon\Carbon::parse($pembayaran->expired_at)->isPast()) {
            return redirect()->route('penyewa.pembayaran')->with('error', 'Waktu pembayaran telah habis. Pesanan ini telah otomatis dibatalkan.');
        }

        return view('penyewa.payment-gateway', compact('pembayaran'));
    }

    public function prosesBayar(Request $request, $id)
    {
        $this->cleanupExpiredOrders();

        $pembayaran = \App\Models\Pembayaran::findOrFail($id);
        $isAjax = $request->ajax() || $request->wantsJson();

        if ($pembayaran->status !== 'pending' || ($pembayaran->expired_at && \Carbon\Carbon::parse($pembayaran->expired_at)->isPast())) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Waktu pembayaran telah habis (10 menit). Pesanan ini telah otomatis dibatalkan.'], 422);
            }
            return redirect()->back()->with('error', 'Waktu pembayaran telah habis (10 menit). Pesanan ini telah otomatis dibatalkan.');
        }

        // Ambil metode pembayaran dari request (dari payment gateway simulasi)
        $metodeBayar = $request->input('metode_bayar');
        $validMetode = ['bca_va', 'bni_va', 'mandiri_va', 'gopay', 'ovo', 'qris'];
        $metodeKolom = null;
        if (in_array($metodeBayar, $validMetode)) {
            if (str_contains($metodeBayar, '_va')) {
                $metodeKolom = 'virtual_account';
            } else {
                $metodeKolom = 'dompet_digital';
            }
        }
        
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Simpan kode_bayar simulasi dan metode pembayaran
            $updateData = ['status' => 'paid', 'dibayar_at' => now()];
            if ($metodeKolom) {
                $updateData['metode_bayar'] = $metodeKolom;
                $updateData['kode_bayar'] = 'SIM-' . strtoupper(\Illuminate\Support\Str::random(8));
                // Simpan data webhook simulasi
                $updateData['webhook_data'] = json_encode([
                    'transaction_status' => 'settlement',
                    'payment_type' => $metodeBayar,
                    'transaction_time' => now()->toDateTimeString(),
                    'gross_amount' => $pembayaran->jumlah,
                    'order_id' => $pembayaran->pesanan_id,
                    'fraud_status' => 'accept',
                    'simulated' => true,
                ]);
            }

            $pembayaran->update($updateData);
            
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

            // Logging transaksi pembayaran
            \Illuminate\Support\Facades\DB::table('log')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => $pesanan->pemesan_id,
                'aksi' => 'Pembayaran Berhasil',
                'detail' => 'Pembayaran pesanan #' . substr($pesanan->id, 0, 8) . ' berhasil via ' . ($metodeBayar ?: 'simulasi') . '. Total: Rp ' . number_format($pembayaran->jumlah, 0, ',', '.'),
                'ip_address' => $request->ip(),
                'terjadi_at' => now(),
            ]);

            // Kirim notifikasi ke Penyedia (pemilik barang)
            $firstItem = $pesanan->items->first();
            $namaBarang = $firstItem && $firstItem->barang ? $firstItem->barang->nama : 'Barang';
            $namaPenyewa = $pesanan->pemesan ? $pesanan->pemesan->name : 'Penyewa';

            \App\Models\Notifikasi::create([
                'user_id' => $pesanan->pemilik_id,
                'pesanan_id' => $pesanan->id,
                'untuk_role' => 'penyedia',
                'tipe' => 'pesanan_masuk',
                'judul' => '📦 Pesanan Baru Masuk!',
                'pesan' => 'Barang "' . $namaBarang . '" telah disewa oleh ' . $namaPenyewa . '. Pembayaran telah lunas (Rp ' . number_format($pembayaran->jumlah, 0, ',', '.') . '). Silakan siapkan barang untuk serah terima.',
                'channel' => 'in_app',
                'is_read' => false,
                'dikirim_at' => now(),
            ]);

            // Kirim notifikasi ke Penyewa (konfirmasi pembayaran)
            \App\Models\Notifikasi::create([
                'user_id' => $pesanan->pemesan_id,
                'pesanan_id' => $pesanan->id,
                'untuk_role' => 'penyewa',
                'tipe' => 'pembayaran_berhasil',
                'judul' => '🎉 Pembayaran Berhasil',
                'pesan' => 'Pembayaran untuk pesanan #' . substr($pesanan->id, 0, 8) . ' berhasil diselesaikan. Silakan temui penyedia untuk serah terima barang.',
                'channel' => 'in_app',
                'is_read' => false,
                'dikirim_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil diselesaikan! Pesanan Anda telah terkonfirmasi.'
                ]);
            }

            return redirect()->route('penyewa.pembayaran')->with('success', 'Pembayaran berhasil diselesaikan! Pesanan Anda telah terkonfirmasi.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Membatalkan pesanan pending secara manual oleh penyewa
     */
    public function batalkanPesanan($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $pembayaran = \App\Models\Pembayaran::where('id', $id)
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemesan_id', $user->id);
            })
            ->where('status', 'pending')
            ->firstOrFail();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $pembayaran->update(['status' => 'expired']);
            if ($pembayaran->pesanan) {
                $pembayaran->pesanan->update(['status' => 'cancelled']);
                foreach ($pembayaran->pesanan->items as $item) {
                    $item->update(['status' => 'cancelled']);
                    if ($item->inventoryLock) {
                        $item->inventoryLock->update(['is_active' => false]);
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan dan jadwal booking barang telah dilepas.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Proses Sewa Langsung (Direct Rent)
     */
    public function prosesSewa(Request $request)
    {
        $this->cleanupExpiredOrders();

        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $barang = \App\Models\Barang::findOrFail($request->barang_id);

        if ($barang->user_id == $user->id) {
            return back()->with('error', 'Anda tidak dapat menyewa barang milik Anda sendiri.');
        }

        if ($barang->status !== 'active') {
            return back()->with('error', 'Barang tidak tersedia untuk disewa saat ini.');
        }

        // Cek apakah user yang sama sudah punya pesanan pending untuk barang ini
        $existingPending = \App\Models\PesananItem::where('barang_id', $barang->id)
            ->whereHas('pesanan', function($q) use ($user) {
                $q->where('pemesan_id', $user->id)->where('status', 'pending_payment');
            })
            ->whereHas('inventoryLock', function($q) {
                $q->where('is_active', true)->where('expired_at', '>', now());
            })
            ->first();

        if ($existingPending) {
            return redirect()->route('penyewa.pembayaran')->with('info', 'Anda sudah memiliki tagihan pending untuk barang ini. Silakan selesaikan pembayaran atau batalkan pesanan tersebut.');
        }

        $mulai = \Carbon\Carbon::parse($request->tanggal_mulai)->startOfDay();
        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai)->startOfDay();
        $hari = $mulai->diffInDays($selesai);
        if ($hari == 0) $hari = 1;

        if ($hari < $barang->min_durasi_sewa || $hari > $barang->max_durasi_sewa) {
            return back()->with('error', "Durasi sewa harus antara {$barang->min_durasi_sewa} - {$barang->max_durasi_sewa} hari.");
        }

        // Cek overlap ketersediaan inventory
        $overlap = \Illuminate\Support\Facades\DB::table('inventory_lock')
            ->join('pesanan_item', 'inventory_lock.pesanan_item_id', '=', 'pesanan_item.id')
            ->where('inventory_lock.barang_id', $barang->id)
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
            return back()->with('error', 'Maaf, tanggal yang dipilih sedang dibooking/dikunci oleh penyewa lain.');
        }

        $subtotal = $hari * $barang->harga_per_hari;
        $biayaLayanan = 2000;
        $total_biaya = $subtotal + $biayaLayanan;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $pesanan = \App\Models\Pesanan::create([
                'pemesan_id' => $user->id,
                'pemilik_id' => $barang->user_id,
                'total_biaya' => $total_biaya,
                'status' => 'pending_payment',
            ]);

            $item = \App\Models\PesananItem::create([
                'pesanan_id' => $pesanan->id,
                'barang_id' => $barang->id,
                'durasi_hari' => $hari,
                'harga_per_hari' => $barang->harga_per_hari,
                'subtotal' => $subtotal,
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
                'status' => 'pending',
            ]);

            // TTL 10 Menit untuk kunci inventaris
            \App\Models\InventoryLock::create([
                'barang_id' => $barang->id,
                'pesanan_item_id' => $item->id,
                'user_id' => $user->id,
                'expired_at' => now()->addMinutes(10),
                'is_active' => true,
            ]);

            // TTL 10 Menit untuk tagihan pembayaran
            $pembayaran = \App\Models\Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'jumlah' => $total_biaya,
                'status' => 'pending',
                'expired_at' => now()->addMinutes(10),
            ]);

            \App\Models\Transaksi::create([
                'pesanan_id' => $pesanan->id,
                'pembayaran_id' => $pembayaran->id,
                'status_escrow' => 'ditahan',
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('penyewa.pembayaran')->with('success', 'Booking berhasil! Anda memiliki waktu 10 menit untuk menyelesaikan pembayaran.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal memproses sewa: ' . $e->getMessage());
        }
    }

    /* --------------------------------------------------------
     * Keranjang Methods
     * -------------------------------------------------------- */

    public function keranjang()
    {
        $this->cleanupExpiredOrders();

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
        $this->cleanupExpiredOrders();

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
        $this->cleanupExpiredOrders();

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
                    throw new \Exception("Maaf, barang '{$k->barang->nama}' sedang dibooking/dikunci pada rentang tanggal tersebut oleh penyewa lain.");
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

            // 3. Buat Items & Lock (TTL 10 Menit)
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
                    'expired_at' => now()->addMinutes(10),
                    'is_active' => true
                ]);
            }

            // 4. Buat Pembayaran (TTL 10 Menit)
            $pembayaran = \App\Models\Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'jumlah' => $totalPembayaran,
                'status' => 'pending',
                'expired_at' => now()->addMinutes(10)
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
