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
            $query->where('kota', 'like', '%' . $request->kota . '%');
        }

        $items = $query->latest()->get();

        $user_wishlist = auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->pluck('barang_id')->toArray() : [];

        return view('penyewa.cari-barang', [
            'items'    => $items,
            'query'    => $request->get('q', ''),
            'kategori' => $request->get('kategori', 'semua'),
            'kota'     => $request->get('kota', ''),
            'user_wishlist' => $user_wishlist,
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
     * Halaman Pembayaran (dummy)
     */
    public function pembayaran()
    {
        $user = auth()->user();
        
        $pesananPending = \App\Models\Pesanan::with(['items.barang.user'])
            ->where('pemesan_id', $user->id)
            ->where('status', 'pending_payment')
            ->get();
            
        $riwayatPembayaran = \App\Models\Pesanan::with(['items.barang'])
            ->where('pemesan_id', $user->id)
            ->where('status', '!=', 'pending_payment')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalBulanIni = $riwayatPembayaran
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_biaya');
            
        return view('penyewa.pembayaran', compact('pesananPending', 'riwayatPembayaran', 'totalBulanIni'));
    }

    public function prosesSewa(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $barang = \App\Models\Barang::findOrFail($request->barang_id);

        if ($barang->status !== 'active') {
            return back()->with('error', 'Barang tidak tersedia untuk disewa saat ini.');
        }

        $mulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $hari = $mulai->diffInDays($selesai);
        if ($hari == 0) $hari = 1; // Minimum 1 hari

        if ($hari < $barang->min_durasi_sewa || $hari > $barang->max_durasi_sewa) {
            return back()->with('error', "Durasi sewa harus antara {$barang->min_durasi_sewa} - {$barang->max_durasi_sewa} hari.");
        }

        $total_biaya = $hari * $barang->harga_per_hari;

        \Illuminate\Support\Facades\DB::transaction(function() use ($barang, $mulai, $selesai, $total_biaya) {
            $pesanan = \App\Models\Pesanan::create([
                'pemesan_id' => auth()->id(),
                'status' => 'pending_payment',
                'total_biaya' => $total_biaya,
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
            ]);

            \App\Models\PesananItem::create([
                'pesanan_id' => $pesanan->id,
                'barang_id' => $barang->id,
                'kuantitas' => 1,
                'harga_satuan' => $barang->harga_per_hari,
                'subtotal' => $total_biaya,
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
            ]);
        });

        return redirect()->route('penyewa.pembayaran')->with('success', 'Permintaan sewa berhasil! Silakan selesaikan pembayaran.');
    }
}
