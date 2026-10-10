<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'nama' => 'Fotografi',
                'slug' => 'fotografi',
                'deskripsi' => 'Peralatan fotografi seperti kamera, lensa, tripod, dan lighting'
            ],
            [
                'nama' => 'Elektronik',
                'slug' => 'elektronik',
                'deskripsi' => 'Perangkat elektronik seperti laptop, monitor, speaker, dan gadget'
            ],
            [
                'nama' => 'Peralatan Rumah Tangga',
                'slug' => 'peralatan-rumah-tangga',
                'deskripsi' => 'Peralatan dapur, ruang tamu, kamar tidur, dan perlengkapan rumah lainnya'
            ],
            [
                'nama' => 'Furnitur',
                'slug' => 'furnitur',
                'deskripsi' => 'Meja, kursi, lemari, bed, dan furniture rumah tangga lainnya'
            ],
            [
                'nama' => 'Peralatan Olahraga',
                'slug' => 'peralatan-olahraga',
                'deskripsi' => 'Sepeda, skateboard, peralatan gym, dan equipment olahraga'
            ],
            [
                'nama' => 'Dekorasi',
                'slug' => 'dekorasi',
                'deskripsi' => 'Hiasan dinding, lampu dekorasi, tanaman hias, dan perlengkapan dekorasi'
            ],
            [
                'nama' => 'Alat Musik',
                'slug' => 'alat-musik',
                'deskripsi' => 'Gitar, keyboard, drum, microphone, dan instrumen musik lainnya'
            ],
            [
                'nama' => 'Perlengkapan Event',
                'slug' => 'perlengkapan-event',
                'deskripsi' => 'Sound system, proyektor, dekorasi event, dan peralatan acara'
            ],
            [
                'nama' => 'Perlengkapan Outdoor',
                'slug' => 'perlengkapan-outdoor',
                'deskripsi' => 'Peralatan untuk kegiatan di luar ruangan seperti piknik, camping dan hiking'
            ],
            [
                'nama' => 'Transportasi',
                'slug' => 'transportasi',
                'deskripsi' => 'Transportasi seperti mobil, motor, sepeda, dan alat transportasi lainnya'
            ],
            [
                'nama' => 'Lainnya',
                'slug' => 'lainnya',
                'deskripsi' => 'Kategori lainnya yang tidak termasuk dalam kategori di atas'
            ]
        ];

        foreach ($categories as $category) {
            DB::table('kategori')->insert([
                'id' => Str::uuid(),
                'nama' => $category['nama'],
                'slug' => $category['slug'],
                'deskripsi' => $category['deskripsi'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
