@extends('layouts.auth', ['title' => 'Registrasi'])

@section('form')
    <form class="auth-form" action="{{ route('register.store') }}" method="post">
        @csrf
        @if ($errors->any())
            <p class="auth-feedback" role="alert">{{ $errors->first() }}</p>
        @endif
        <div class="auth-fields">
            <label class="sr-only" for="name">Nama</label>
            <input class="auth-input" id="name" name="name" type="text" placeholder="Nama" autocomplete="name" value="{{ old('name') }}" required>
            <label class="sr-only" for="email">Email</label>
            <input
                class="auth-input"
                id="email"
                name="email"
                type="email"
                placeholder="Email"
                autocomplete="email"
                value="{{ old('email') }}"
                required
            >

            <div class="password-field">
                <label class="sr-only" for="password">Password</label>
                <input
                    class="auth-input"
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Password"
                    autocomplete="new-password"
                    required
                    data-password-input
                >
                <button
                    class="password-toggle"
                    type="button"
                    aria-label="Tampilkan password"
                    aria-controls="password"
                    data-password-toggle
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true" data-eye>
                        <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" />
                        <circle cx="12" cy="12" r="2.5" />
                    </svg>
                    <svg viewBox="0 0 24 24" aria-hidden="true" data-eye-off hidden>
                        <path d="m3 3 18 18M10.6 6.2A10.3 10.3 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16 16 0 0 1-3.1 3.8M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.4 6 9.5 6c1.1 0 2.1-.2 3-.6" />
                        <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" />
                    </svg>
                </button>
            </div>

            <label class="sr-only" for="password_confirmation">Re-Password</label>
            <input
                class="auth-input"
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                placeholder="Re-Password"
                autocomplete="new-password"
                required
                data-password-confirmation
            >
        </div>

        <button class="auth-submit" type="submit">Registrasi</button>
        <p class="auth-feedback" role="status" aria-live="polite" data-auth-feedback></p>
    </form>
@endsection

@section('switch')
    <p>Sudah Punya Akun?</p>
    <a href="{{ route('login') }}">Masuk</a>
@endsection
