@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
<div class="auth-card" id="register-card">
    <div class="auth-card-header">
        {{-- Role badge --}}
        @if($role === 'penyedia')
            <div class="role-badge penyedia">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Penyedia Barang
            </div>
        @else
            <div class="role-badge penyewa">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Penyewa Barang
            </div>
        @endif

        <h1>Buat Akun Baru</h1>
        <p>Isi formulir di bawah untuk mendaftar</p>
    </div>

    {{-- Error --}}
    @if ($errors->any())
    <div class="alert-error" id="register-alert">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="register-form">
        @csrf
        <input type="hidden" name="role" value="{{ $role }}">

        {{-- Nama --}}
        <div class="form-group">
            <label class="form-label" for="register-name">Nama Lengkap</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <input type="text" class="form-input" id="register-name" name="name" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required autocomplete="name" autofocus>
            </div>
            @error('name')
                <div class="form-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="register-email">Email</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input type="email" class="form-input" id="register-email" name="email" placeholder="nama@email.com" value="{{ old('email') }}" required autocomplete="email">
            </div>
            @error('email')
                <div class="form-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
            @enderror
        </div>

        {{-- Phone --}}
        <div class="form-group">
            <label class="form-label" for="register-phone">No. Handphone <span style="color: var(--text-muted); font-weight: 400;">(opsional)</span></label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <input type="tel" class="form-input" id="register-phone" name="phone" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" autocomplete="tel">
            </div>
            @error('phone')
                <div class="form-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="register-password">Password</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input type="password" class="form-input" id="register-password" name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password" style="padding-right: 2.75rem;">
                <button type="button" class="password-toggle" onclick="togglePassword('register-password', this)" aria-label="Toggle password">
                    <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            <div class="password-strength" id="password-strength">
                <div class="strength-bar"><div class="strength-bar-fill" id="strength-bar-fill"></div></div>
                <span class="strength-text" id="strength-text"></span>
            </div>
            @error('password')
                <div class="form-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label class="form-label" for="register-password-confirm">Konfirmasi Password</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <input type="password" class="form-input" id="register-password-confirm" name="password_confirmation" placeholder="Ulangi password Anda" required autocomplete="new-password" style="padding-right: 2.75rem;">
                <button type="button" class="password-toggle" onclick="togglePassword('register-password-confirm', this)" aria-label="Toggle password">
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

        {{-- Terms --}}
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-check" id="terms-check">
                <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                <span class="form-check-label">Saya setuju dengan <a href="#">Syarat & Ketentuan</a> serta <a href="#">Kebijakan Privasi</a></span>
            </label>
            @error('terms')
                <div class="form-error" style="margin-left: 1.5rem;"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary {{ $role }}" id="register-submit">
            <span>
                Daftar Sekarang
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </span>
        </button>
    </form>

    <div class="auth-footer">
        Sudah punya akun? <a href="{{ route('login', ['role' => $role]) }}">Masuk di sini</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var passwordInput = document.getElementById('register-password');
    var strengthFill = document.getElementById('strength-bar-fill');
    var strengthText = document.getElementById('strength-text');

    passwordInput.addEventListener('input', function () {
        var val = this.value;
        var score = 0;

        if (val.length === 0) {
            strengthFill.style.width = '0';
            strengthText.textContent = '';
            return;
        }

        if (val.length >= 8) score++;
        if (val.length >= 12) score++;
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[^a-zA-Z0-9]/.test(val)) score++;

        if (score <= 1) {
            strengthFill.style.width = '20%';
            strengthFill.style.background = '#DC2626';
            strengthText.style.color = '#DC2626';
            strengthText.textContent = 'Lemah';
        } else if (score <= 2) {
            strengthFill.style.width = '40%';
            strengthFill.style.background = '#D97706';
            strengthText.style.color = '#D97706';
            strengthText.textContent = 'Cukup';
        } else if (score <= 3) {
            strengthFill.style.width = '65%';
            strengthFill.style.background = '#2563EB';
            strengthText.style.color = '#2563EB';
            strengthText.textContent = 'Bagus';
        } else {
            strengthFill.style.width = '100%';
            strengthFill.style.background = '#059669';
            strengthText.style.color = '#059669';
            strengthText.textContent = 'Kuat';
        }
    });
</script>
@endpush
