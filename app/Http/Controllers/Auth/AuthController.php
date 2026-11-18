<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Fire Registered event — Laravel listener akan otomatis kirim email verifikasi
        event(new Registered($user));

        // Login user setelah registrasi agar bisa akses halaman verification.notice
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.');
    }

    /**
     * Switch mode aktif antara penyewa ↔ penyedia.
     * Disimpan di session, bukan di database.
     */
    public function switchMode(Request $request)
    {
        $targetMode = $request->input('target_mode');
        if (!in_array($targetMode, ['penyewa', 'penyedia'])) {
            $currentMode = session('active_mode', 'penyewa');
            $targetMode = $currentMode === 'penyewa' ? 'penyedia' : 'penyewa';
        }

        session(['active_mode' => $targetMode]);

        $redirect = $targetMode === 'penyedia'
            ? route('penyedia.daftar-barang')
            : route('penyewa.cari-barang');

        // HTTP 303 See Other: Turbo Drive mengikuti redirect dalam 1 langkah cepat (Single Request)
        if ($request->expectsJson() && !$request->header('Turbo-Frame') && !$request->header('Accept-Encoding')) {
            return response()->json([
                'mode'     => $targetMode,
                'redirect' => $redirect,
            ]);
        }

        return redirect()->to($redirect, 303);
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
     * Dashboard utama — redirect berdasarkan role/mode aktif session.
     * Admin      → manajemen-user
     * Penyedia   → daftar-barang
     * Penyewa    → cari-barang
     */
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.manajemen-user');
        }

        $mode = session('active_mode', 'penyewa');

        if ($mode === 'penyedia') {
            return redirect()->route('penyedia.daftar-barang');
        }

        return redirect()->route('penyewa.cari-barang');
    }
}
