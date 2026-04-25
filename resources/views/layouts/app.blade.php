<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Absensi dan Penggajian Digital - Kelola absensi karyawan dan penggajian secara efisien">
    <title>@yield('title', 'Dashboard') — Garuda Jaya</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- App CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="#" class="sidebar-brand">
                    <i class="fa-solid fa-fingerprint"></i>
                    <span>Garuda <strong style="color:var(--primary-500)">Jaya</strong></span>
                </a>
            </div>

            <nav class="sidebar-nav">
                @yield('sidebar-nav')
            </nav>

            <div class="user-profile-widget">
                <div class="avatar">
                    {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ session('user_name', 'Pengguna') }}</div>
                    <div class="user-role">{{ ucfirst(session('user_role', 'guest')) }}</div>
                </div>
                <a href="{{ route('logout') }}" style="margin-left:auto;color:var(--text-muted);transition:color 0.2s;" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="topbar-right">
                    <!-- Dark Mode Toggle -->
                    <button class="icon-btn" id="themeToggle" title="Toggle Dark Mode" aria-label="Toggle Dark Mode">
                        <i class="fa-solid fa-moon" id="themeIcon"></i>
                    </button>
                    <!-- Notifications -->
                    <button class="icon-btn" title="Notifikasi" aria-label="Notifikasi">
                        <i class="fa-solid fa-bell"></i>
                    </button>
                    <!-- User Avatar -->
                    <div class="avatar" style="cursor:pointer;font-size:0.85rem;" title="{{ session('user_name', 'Pengguna') }}">
                        {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="content-area">
                @if(session('success'))
                    <div class="alert alert-success" style="background:color-mix(in srgb, var(--success) 10%, transparent);border:1px solid var(--success);color:var(--success);padding:1rem 1.5rem;border-radius:var(--border-radius-sm);margin-bottom:1.5rem;display:flex;align-items:center;gap:0.75rem;">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="background:color-mix(in srgb, var(--danger) 10%, transparent);border:1px solid var(--danger);color:var(--danger);padding:1rem 1.5rem;border-radius:var(--border-radius-sm);margin-bottom:1.5rem;display:flex;align-items:center;gap:0.75rem;">
                        <i class="fa-solid fa-circle-xmark"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- App JS -->
    @stack('scripts')
</body>
</html>
