<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Mail\VerifyEmailMagicLink;
use Carbon\Carbon;

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

            // Cek jika akun disuspend
            if ($user->status === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()
                    ->withInput($request->only('email', 'remember', '_form'))
                    ->withErrors(['email' => 'Akun Anda ditangguhkan karena melanggar kebijakan. Hubungi admin.']);
            }

            // Cek apakah email sudah terverifikasi (Admin tidak perlu verifikasi)
            if (!$user->isAdmin() && is_null($user->email_verified_at)) {
                Auth::logout();
                return back()
                    ->withInput($request->only('email', 'remember', '_form'))
                    ->withErrors(['email' => 'Akun belum aktif. Silakan cek email Anda (termasuk folder spam) untuk tautan verifikasi.']);
            }

            $request->session()->regenerate();

            // Set default mode untuk user non-admin
            if (!$user->isAdmin()) {
                session(['active_mode' => 'penyewa']);
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil! Selamat datang kembali.');
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

        // Cegah registrasi email yang sama jika sudah diverifikasi
        if (User::where('email', $validated['email'])->exists()) {
            return back()->withInput()->withErrors(['email' => 'Email ini sudah terdaftar.']);
        }

        // Generate Token
        $token = Str::random(60);

        $userData = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
            'token'    => $token
        ];

        // Simpan ke Cache selama 60 menit (cukup waktu user cek email)
        Cache::put('register_' . $token, $userData, now()->addMinutes(60));

        // Kirim email verifikasi magic link
        try {
            Mail::to($userData['email'])->send(new VerifyEmailMagicLink($userData));
        } catch (\Exception $e) {
            Cache::forget('register_' . $token);
            \Illuminate\Support\Facades\Log::error('SMTP Error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['email' => 'Gagal mengirim email. Pastikan Anda sudah memasukkan Email dan "App Password" Gmail yang asli di dalam file .env']);
        }

        return redirect()->route('verification.notice', ['token' => $token]);
    }

    /**
     * Tampilkan halaman notifikasi cek email
     */
    public function verifyNotice($token)
    {
        if (!Cache::has('register_' . $token)) {
            return redirect()->route('register')->withErrors(['email' => 'Sesi registrasi telah kedaluwarsa atau tidak valid. Silakan daftar ulang.']);
        }
        return view('auth.verify-notice', compact('token'));
    }

    /**
     * Verifikasi tautan dari email (Magic Link)
     */
    public function verifyEmail($token, Request $request)
    {
        $userData = Cache::get('register_' . $token);
        
        if (!$userData) {
            return redirect()->route('register')->withErrors(['email' => 'Tautan verifikasi sudah kedaluwarsa (lebih dari 60 menit). Silakan daftar ulang.']);
        }

        return $this->finalizeRegistration($userData, $token);
    }

    /**
     * Fungsi helper untuk memindahkan data dari Cache ke Database
     */
    private function finalizeRegistration($userData, $token)
    {
        // Pastikan email belum terdaftar (mencegah double-click)
        if (User::where('email', $userData['email'])->exists()) {
            Cache::forget('register_' . $token);
            return redirect()->route('login')->withErrors(['email' => 'Email ini sudah terdaftar.']);
        }

        $user = User::create([
            'name'     => $userData['name'],
            'email'    => $userData['email'],
            'phone'    => $userData['phone'],
            'password' => $userData['password'],
            'role'     => $userData['role'],
        ]);
        
        $user->email_verified_at = Carbon::now();
        $user->save();

        // Hapus sampah dari Cache
        Cache::forget('register_' . $token);

        // Mengarahkan pengguna kembali ke halaman login
        return redirect()->route('login')->with('success', 'Verifikasi berhasil! Akun Anda sudah resmi terdaftar. Silakan login.');
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

        // Redirect ke halaman utama mode yang baru aktif,
        // bukan back() yang mengarahkan ke halaman mode lama.
        if ($newMode === 'penyedia') {
            return redirect()->route('penyedia.daftar-barang');
        }

        return redirect()->route('penyewa.cari-barang');
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

        // Redirect admin langsung ke halaman dashboard (platform analytics)
        if ($user->isAdmin()) {
            return redirect()->route('admin.platform-analytics');
        }

        $mode = session('active_mode', 'penyewa');

        // Redirect berdasarkan mode aktif
        if ($mode === 'penyedia') {
            return redirect()->route('penyedia.daftar-barang');
        }

        return redirect()->route('penyewa.cari-barang');
    }
}
