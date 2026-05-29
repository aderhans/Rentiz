@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<div class="auth-card" id="login-card">
    <div class="auth-card-header">
        {{-- Role badge --}}
        @if($role === 'penyedia')
            <div class="role-badge penyedia">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Penyedia Barang
            </div>
        @elseif($role === 'admin')
            <div class="role-badge admin">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Administrator
            </div>
        @else
            <div class="role-badge penyewa">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Penyewa Barang
            </div>
        @endif

        <h1>Masuk ke Akun</h1>
        <p>Silakan masukkan email dan password Anda</p>
    </div>

    {{-- Error --}}
    @if ($errors->any())
    <div class="alert-error" id="login-alert">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf
        <input type="hidden" name="role" value="{{ $role }}">

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="login-email">Email</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input type="email" class="form-input" id="login-email" name="email" placeholder="nama@email.com" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="login-password">Password</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input type="password" class="form-input" id="login-password" name="password" placeholder="Masukkan password" required autocomplete="current-password" style="padding-right: 2.75rem;">
                <button type="button" class="password-toggle" onclick="togglePassword('login-password', this)" aria-label="Toggle password">
                    <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember --}}
        <div class="form-row-between">
            <label class="form-check" id="remember-check">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="form-check-label">Ingat saya</span>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary {{ $role }}" id="login-submit">
            <span>
                Masuk
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </span>
        </button>
    </form>


</div>
@endsection
