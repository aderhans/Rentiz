@extends('layouts.app')
@section('title', 'Laporan & Dispute')
@section('breadcrumb', 'Laporan & Dispute')

@push('styles')
<style>
    .dispute-tabs { display:flex;gap:0;background:white;border:1px solid var(--card-border);border-radius:var(--radius-md);padding:4px;margin-bottom:1.5rem;overflow-x:auto; }
    .dispute-tab { flex:1;min-width:120px;padding:.5rem .85rem;border-radius:calc(var(--radius-md) - 4px);border:none;background:transparent;font-size:.81rem;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:all 200ms;white-space:nowrap;font-family:var(--font-body);display:flex;align-items:center;justify-content:center;gap:5px; }
    .dispute-tab .tb { background:var(--card-border);color:var(--text-muted);font-size:.66rem;padding:1px 5px;border-radius:50px; }
    .dispute-tab.active { background:var(--color-admin);color:white; }
    .dispute-tab.active .tb { background:rgba(255,255,255,.25);color:white; }

    .dispute-card { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1rem;display:flex;align-items:flex-start;gap:1.25rem;transition:box-shadow 200ms; }
    .dispute-card:hover { box-shadow:var(--shadow-sm); }
    .dispute-card.border-l-new { border-left:4px solid var(--danger); }
    .dispute-card.border-l-proses { border-left:4px solid var(--warning); }
    .dispute-card.border-l-done { border-left:4px solid var(--success); }

    .d-icon { width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0; }
    .d-info { flex:1;min-width:0; }
    .d-title { font-weight:700;font-size:.95rem;color:var(--text);display:flex;align-items:center;gap:.5rem; }
    .d-id { font-family:monospace;font-size:.7rem;color:var(--text-muted);background:var(--content-bg);padding:2px 6px;border-radius:4px;font-weight:400; }
    .d-meta { display:flex;align-items:center;gap:.75rem;margin-top:4px;font-size:.78rem;color:var(--text-secondary); }
    .d-desc { margin-top:.75rem;font-size:.82rem;color:var(--text);line-height:1.5;background:#F8FAFF;padding:.75rem 1rem;border-radius:var(--radius-sm);border:1px solid #E2E8F0; }
    
    .d-parties { display:flex;align-items:center;gap:1.5rem;margin-top:.75rem; }
    .d-party { display:flex;align-items:center;gap:.5rem; }
    .dp-ava { width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:white; }
    .dp-name { font-size:.75rem;font-weight:600;color:var(--text); }
    .dp-role { font-size:.65rem;color:var(--text-muted); }

    .d-actions { display:flex;flex-direction:column;gap:.5rem;min-width:140px;flex-shrink:0; }
    .btn-action { width:100%;padding:.5rem;border-radius:var(--radius-sm);font-size:.78rem;font-weight:600;cursor:pointer;font-family:var(--font-body);transition:all 150ms;text-align:center;border:1px solid transparent; }
    .btn-primary { background:var(--color-admin);color:white; }
    .btn-primary:hover { background:#6D28D9; }
    .btn-secondary { background:white;border-color:var(--card-border);color:var(--text-secondary); }
    .btn-secondary:hover { background:var(--content-bg);color:var(--text); }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;">
    <div>
        <h1 class="page-title">⚖️ Laporan & Dispute</h1>
        <p class="page-subtitle">Tangani keluhan, sengketa transaksi, dan laporan pelanggaran</p>
    </div>
    @if($kasusBaru > 0 || $kasusProses > 0)
    <span class="badge badge-danger" style="font-size:.82rem;padding:6px 14px;">{{ $kasusBaru + $kasusProses }} Kasus Aktif</span>
    @endif
</div>

<div class="dispute-tabs">
    <button class="dispute-tab active" onclick="switchTab('semua',this)">Semua <span class="tb">{{ $totalCases }}</span></button>
    <button class="dispute-tab" onclick="switchTab('baru',this)">Kasus Baru <span class="tb">{{ $kasusBaru }}</span></button>
    <button class="dispute-tab" onclick="switchTab('proses',this)">Dalam Proses <span class="tb">{{ $kasusProses }}</span></button>
    <button class="dispute-tab" onclick="switchTab('selesai',this)">Selesai <span class="tb">{{ $kasusSelesai }}</span></button>
</div>

<div id="dispute-list">
    @forelse($cases as $c)
    @php
        $statusMap = [
            'open' => ['bg' => '#FEE2E2', 'icon' => '🚨', 'border' => 'baru', 'class' => 'baru', 'label' => 'Menunggu Review', 'badge' => 'danger'],
            'under_review' => ['bg' => '#FEF3C7', 'icon' => '⏳', 'border' => 'proses', 'class' => 'proses', 'label' => 'Sedang Diinvestigasi', 'badge' => 'warning'],
            'pertimbangan_diberikan' => ['bg' => '#FEF3C7', 'icon' => '⏳', 'border' => 'proses', 'class' => 'proses', 'label' => 'Menunggu Pihak Terkait', 'badge' => 'warning'],
            'closed' => ['bg' => '#ECFDF5', 'icon' => '🧹', 'border' => 'done', 'class' => 'selesai', 'label' => 'Resolved', 'badge' => 'success']
        ];
        $meta = $statusMap[$c->status];
    @endphp
    <div class="dispute-card border-l-{{ $meta['border'] }}" data-status="{{ $meta['class'] }}" id="case-{{ $loop->index }}">
        <div class="d-icon" style="background:{{ $meta['bg'] }};">{{ $meta['icon'] }}</div>
        <div class="d-info">
            <div class="d-title">{{ ucwords(str_replace('_', ' ', $c->kategori_laporan)) }} <span class="d-id">{{ substr($c->id, 0, 8) }}</span></div>
            <div class="d-meta">
                <span class="badge badge-neutral" style="font-size:.65rem;">{{ ucwords(str_replace('_', ' ', $c->kategori_laporan)) }}</span>
                <span>Dilaporkan: {{ $c->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="d-desc">"{{ $c->deskripsi }}"</div>
            <div class="d-parties">
                <div class="d-party">
                    <div class="dp-ava" style="background:#0D9488">{{ substr($c->pelapor->name ?? '?', 0, 1) }}</div>
                    <div><div class="dp-name">{{ $c->pelapor->name ?? 'Unknown' }}</div><div class="dp-role">Pelapor (Penyewa)</div></div>
                </div>
                <div style="font-size:.7rem;color:var(--text-muted);">vs</div>
                <div class="d-party">
                    <div class="dp-ava" style="background:#2563EB">{{ substr($c->terlapor->name ?? '?', 0, 1) }}</div>
                    <div><div class="dp-name">{{ $c->terlapor->name ?? 'Unknown' }}</div><div class="dp-role">Terlapor (Penyedia)</div></div>
                </div>
            </div>
        </div>
        <div class="d-actions">
            @if($c->status === 'closed')
                <div style="text-align:center;padding:.5rem;background:#ECFDF5;border-radius:var(--radius-sm);color:var(--success);font-weight:700;font-size:.78rem;">✓ Resolved</div>
                <button class="btn-action btn-secondary" onclick="showToast('#7C3AED','📄 Membuka log kasus {{ substr($c->id, 0, 8) }}')">Lihat Log Resolusi</button>
            @else
                <div style="text-align:center;margin-bottom:.3rem;">
                    <span class="badge badge-{{ $meta['badge'] }}" style="font-size:.65rem;">{{ $meta['label'] }}</span>
                </div>
                <button class="btn-action btn-primary" onclick="showToast('#7C3AED','🔎 Membuka ruang mediasi {{ substr($c->id, 0, 8) }}')">Ruang Mediasi</button>
                @if($c->status === 'open')
                    <button class="btn-action btn-secondary" onclick="takeCase({{ $loop->index }}, '{{ substr($c->id, 0, 8) }}')">Ambil Kasus</button>
                @endif
                <button class="btn-action btn-secondary" onclick="resolveCase({{ $loop->index }}, '{{ substr($c->id, 0, 8) }}')">Tutup Kasus (Resolve)</button>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center; padding: 4rem 1rem; color: var(--text-muted);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Tidak ada laporan</h3>
        <p>Belum ada sengketa atau laporan yang masuk.</p>
    </div>
    @endforelse
</div>

<div class="toast" id="dsp-toast"></div>
@endsection

@push('scripts')
<script>
    function switchTab(status, btn) {
        document.querySelectorAll('.dispute-tab').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('#dispute-list .dispute-card').forEach(function(c) {
            c.style.display = (status==='semua' || c.dataset.status===status) ? '' : 'none';
        });
    }
    function takeCase(idx, id) {
        var card = document.getElementById('case-'+idx);
        card.className = 'dispute-card border-l-proses';
        card.dataset.status = 'proses';
        card.querySelector('.d-actions').innerHTML = '<div style="text-align:center;margin-bottom:.3rem;"><span class="badge badge-warning" style="font-size:.65rem;">Sedang Diinvestigasi</span></div><button class="btn-action btn-primary" onclick="showToast(\'#7C3AED\',\'🔎 Membuka ruang mediasi '+id+'\')">Ruang Mediasi</button><button class="btn-action btn-secondary" onclick="resolveCase('+idx+', \''+id+'\')">Tutup Kasus (Resolve)</button>';
        showToast('#D97706', '👀 Kamu mengambil alih kasus '+id);
    }
    function resolveCase(idx, id) {
        if(!confirm('Tandai kasus '+id+' sebagai selesai (Resolved)?')) return;
        var card = document.getElementById('case-'+idx);
        card.className = 'dispute-card border-l-done';
        card.dataset.status = 'selesai';
        card.querySelector('.d-actions').innerHTML = '<div style="text-align:center;padding:.5rem;background:#ECFDF5;border-radius:var(--radius-sm);color:var(--success);font-weight:700;font-size:.78rem;">✓ Resolved</div><button class="btn-action btn-secondary" onclick="showToast(\'#7C3AED\',\'📄 Membuka log kasus '+id+'\')">Lihat Log Resolusi</button>';
        showToast('#059669', '✅ Kasus '+id+' berhasil diselesaikan!');
    }
    function showToast(bg, msg) {
        var t = document.getElementById('dsp-toast');
        t.style.background = bg; t.textContent = msg; t.classList.add('show');
        clearTimeout(window._dt); window._dt = setTimeout(function(){ t.classList.remove('show'); }, 3500);
    }
</script>
@endpush
