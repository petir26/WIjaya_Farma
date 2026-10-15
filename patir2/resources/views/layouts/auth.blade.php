<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111a3b">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | Wijaya Farma</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <div class="auth-screen">
        <header class="auth-header">
            <div class="status-bar" aria-hidden="true">
                <span>9:41</span>
                <span class="status-icons">
                    <svg viewBox="0 0 18 13" role="img">
                        <path d="M1 12V9.5M5 12V7M9 12V4.5M13 12V2M17 12V.5" />
                    </svg>
                    <svg viewBox="0 0 18 13" role="img">
                        <path d="M1 4.5a11 11 0 0 1 16 0M4 7.5a7 7 0 0 1 10 0M7 10.5a3 3 0 0 1 4 0" />
                        <circle cx="9" cy="12" r=".7" />
                    </svg>
                    <svg class="battery-icon" viewBox="0 0 24 13" role="img">
                        <rect x="1" y="1" width="19" height="11" rx="2" />
                        <path d="M22 4v5" />
                    </svg>
                </span>
            </div>
            <h1>{{ $title }}</h1>
        </header>

        <section class="auth-brand" aria-label="Wijaya Farma">
            <a class="brand-wordmark" href="{{ route('login') }}" aria-label="Wijaya Farma">
                <span>wijaya</span><span class="brand-accent">farma</span>
            </a>
            <p>Selamat datang! Silakan Login</p>
        </section>

        <main class="auth-main">
            @yield('form')
        </main>

        <footer class="auth-footer">
            @yield('switch')
        </footer>
    </div>
</body>
</html>
