<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     * Admin → halaman login terpisah (auth layout).
     * User biasa → landing page (tab login).
     */
    public function showLoginForm(Request $request)
    {
        // Admin login tetap di halaman terpisah
        if ($request->query('role') === 'admin') {
            return view('auth.login', ['role' => 'admin']);
        }

        return view('landing', ['activeTab' => 'login']);
    }

    /**
     * Handle login request.
     * Admin: cek role di DB.
     * User biasa: langsung login, set session mode.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');
        $isAdminLogin = $request->input('role') === 'admin';

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Jika login via form admin, pastikan user memang admin
            if ($isAdminLogin && !$user->isAdmin()) {
                Auth::logout();
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors(['email' => 'Akun ini bukan akun administrator.']);
            }

            $request->session()->regenerate();

            // Set default mode untuk user non-admin
            if (!$user->isAdmin()) {
                session(['active_mode' => 'penyewa']);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('email', 'remember', '_form'))
            ->withErrors([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
    }

    /**
     * Tampilkan halaman register (landing tab register).
     */
    public function showRegisterForm(Request $request)
    {
        return view('landing', ['activeTab' => 'register']);
    }

    /**
     * Handle registrasi user baru.
     * Tidak ada pemilihan role — semua user default 'penyewa' di DB.
     * User bisa switch mode via sidebar nanti.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms'    => ['accepted'],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'terms.accepted'     => 'Anda harus menyetujui syarat & ketentuan.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role'     => 'user', // Default — user bisa switch mode
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login menggunakan akun baru Anda.');
    }

    /**
     * Switch mode aktif antara penyewa ↔ penyedia.
     * Disimpan di session, bukan di database.
     */
    public function switchMode(Request $request)
    {
        $currentMode = session('active_mode', 'penyewa');
        $newMode = $currentMode === 'penyewa' ? 'penyedia' : 'penyewa';

        session(['active_mode' => $newMode]);

        return redirect()->back();
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Dashboard utama — render berdasarkan mode aktif session.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $currentMode = session('active_mode', 'penyewa');
        $viewData = ['user' => $user];

        if ($currentMode === 'penyedia' && !$user->isAdmin()) {
            $activeListingCount = Barang::where('user_id', $user->id)
                ->where('status', 'active')
                ->count();
            $revenueThisMonth = (float) DB::table('pesanan')
                ->where('pemilik_id', $user->id)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total_biaya');
            $lastMonth = now()->copy()->subMonth();
            $revenueLastMonth = (float) DB::table('pesanan')
                ->where('pemilik_id', $user->id)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->whereYear('created_at', $lastMonth->year)
                ->whereMonth('created_at', $lastMonth->month)
                ->sum('total_biaya');
            $requestCount = DB::table('pesanan')
                ->where('pemilik_id', $user->id)
                ->whereIn('status', ['pending_payment', 'paid', 'confirmed'])
                ->count();
            $requests = DB::table('pesanan as p')
                ->join('pesanan_item as pi', 'p.id', '=', 'pi.pesanan_id')
                ->join('barang as b', 'pi.barang_id', '=', 'b.id')
                ->join('users as u', 'p.pemesan_id', '=', 'u.id')
                ->where('p.pemilik_id', $user->id)
                ->whereIn('p.status', ['pending_payment', 'paid', 'confirmed'])
                ->select([
                    'p.status',
                    'p.total_biaya',
                    'u.name as pemesan_name',
                    'b.nama as barang_name',
                    'pi.tanggal_mulai',
                    'pi.tanggal_selesai',
                    'pi.durasi_hari',
                ])
                ->orderByDesc('p.created_at')
                ->limit(5)
                ->get()
                ->map(function ($row) {
                    $period = date('j M', strtotime($row->tanggal_mulai)) . ' - ' . date('j M', strtotime($row->tanggal_selesai));
                    $statusMap = [
                        'pending_payment' => ['label' => 'Menunggu pembayaran', 'status' => 'warning'],
                        'paid' => ['label' => 'Dibayar', 'status' => 'info'],
                        'confirmed' => ['label' => 'Dikonfirmasi', 'status' => 'success'],
                    ];
                    $meta = $statusMap[$row->status] ?? ['label' => ucfirst($row->status), 'status' => 'neutral'];

                    return [
                        'name' => $row->pemesan_name,
                        'item' => $row->barang_name,
                        'period' => $period,
                        'dur' => $row->durasi_hari . ' hari',
                        'total' => number_format($row->total_biaya, 0, ',', '.'),
                        'label' => $meta['label'],
                        'status' => $meta['status'],
                    ];
                })
                ->toArray();
            $avgRating = DB::table('rating')
                ->join('barang', 'rating.barang_id', '=', 'barang.id')
                ->where('barang.user_id', $user->id)
                ->avg('nilai');

            if ($revenueLastMonth > 0) {
                $percent = round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100);
                $revenueChangeLabel = ($percent > 0 ? '+' : '') . $percent . '% vs bulan lalu';
            } elseif ($revenueThisMonth > 0) {
                $revenueChangeLabel = 'Pendapatan baru bulan ini';
            } else {
                $revenueChangeLabel = 'Belum ada pendapatan';
            }

            $viewData = array_merge($viewData, [
                'activeListingCount' => $activeListingCount,
                'revenueThisMonth' => $revenueThisMonth,
                'revenueChangeLabel' => $revenueChangeLabel,
                'requestCount' => $requestCount,
                'requests' => $requests,
                'avgRating' => $avgRating ? round($avgRating, 1) : 0,
            ]);
        }

        return view('dashboard', $viewData);
    }
}
