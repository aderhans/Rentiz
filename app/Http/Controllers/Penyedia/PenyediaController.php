<?php

namespace App\Http\Controllers\Penyedia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenyediaController extends Controller
{
    public function daftarBarang()
    {
        $items = \App\Models\Barang::where('user_id', auth()->id())->get();
        return view('penyedia.daftar-barang', compact('items'));
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
            'image' => 'required|image|max:2048' // max 2MB
        ]);

        $path = $request->file('image')->store('public/items');

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

        \App\Models\FotoBarang::create([
            'barang_id' => $barang->id,
            'path_foto' => str_replace('public/', '', $path),
            'is_primary' => true
        ]);

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
        return view('penyedia.analitik');
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
