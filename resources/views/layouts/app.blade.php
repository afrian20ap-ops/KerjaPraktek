<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Absensi dan Penggajian Digital - Kelola absensi karyawan dan penggajian secara efisien">
    <title>@yield('title', 'Dashboard') — Garuda Jaya</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/garuda.ico') }}">

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
                    <img id="sidebar-logo" src="{{ asset('images/garuda.png') }}" alt="Logo" style="height: 32px; width: auto; object-fit: contain;">
                    <span>Garuda <strong style="color:var(--primary-500)">Jaya</strong></span>
                </a>
            </div>

            <nav class="sidebar-nav">
                @yield('sidebar-nav')
            </nav>

            <div class="user-profile-widget">
                <div class="avatar" style="overflow: hidden;">
                    @if(session('user_foto'))
                        <img src="{{ asset('storage/' . session('user_foto')) }}" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                    @endif
                </div>
                <div class="user-info">
                    <div class="user-name">{{ session('user_name', 'Pengguna') }}</div>
                    <div class="user-role">{{ ucfirst(session('user_role', 'guest')) }}</div>
                </div>
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

                    <!-- User Profile Dropdown -->
                    <div class="dropdown" style="position: relative;">
                        <div class="avatar" style="cursor:pointer;font-size:0.85rem;overflow:hidden;" title="{{ session('user_name', 'Pengguna') }}" onclick="toggleDropdown('profileMenu', event)">
                            @if(session('user_foto'))
                                <img src="{{ asset('storage/' . session('user_foto')) }}" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                            @endif
                        </div>
                        <div class="dropdown-menu profile-menu" id="profileMenu">
                            <div class="dropdown-header profile-header" style="display: flex; align-items: center; justify-content: flex-start; gap: 1rem; padding: 1rem; border-bottom: 1px solid var(--border-color);">
                                <div class="avatar" style="width: 48px; height: 48px; font-size: 1.25rem; flex-shrink: 0; overflow: hidden;">
                                    @if(session('user_foto'))
                                        <img src="{{ asset('storage/' . session('user_foto')) }}" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                                    @endif
                                </div>
                                <div class="profile-info" style="text-align: left;">
                                    <div class="user-name" style="font-size:1rem; font-weight:700; color:var(--text-primary);">{{ session('user_name', 'Pengguna') }}</div>
                                    <div class="user-role" style="font-size:0.8rem; color:var(--text-secondary);">{{ ucfirst(session('user_role', 'guest')) }}</div>
                                </div>
                            </div>
                            <div class="dropdown-content">
                                <a href="{{ route('profile') }}" class="dropdown-item">
                                    <i class="fa-regular fa-user"></i> Profil Saya
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger" style="width:100%; text-align:left; border:none; background:none; cursor:pointer;">
                                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="content-area">
                @if(session('success'))
                    <div class="alert-card alert-success">
                        <i class="fa-solid fa-circle-check" style="font-size:1.25rem;"></i>
                        <div style="flex:1;">
                            <strong style="display:block;margin-bottom:0.25rem;">Berhasil!</strong>
                            <span style="font-size:0.9rem;opacity:0.9;">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert-card alert-danger">
                        <i class="fa-solid fa-circle-xmark" style="font-size:1.25rem;"></i>
                        <div style="flex:1;">
                            <strong style="display:block;margin-bottom:0.25rem;">Terjadi Kesalahan</strong>
                            <span style="font-size:0.9rem;opacity:0.9;">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert-card alert-danger" style="margin-bottom:1rem;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size:1.25rem;"></i>
                        <div style="flex:1;">
                            <strong style="display:block;margin-bottom:0.25rem;">Terjadi Kesalahan Validasi</strong>
                            <ul style="margin:0; padding-left:1.25rem; font-size:0.9rem; opacity:0.9;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- App JS -->
    <script>
        function toggleDropdown(id, event) {
            event.stopPropagation();
            const menus = document.querySelectorAll('.dropdown-menu');
            menus.forEach(menu => {
                if(menu.id !== id) {
                    menu.classList.remove('show');
                }
            });
            document.getElementById(id).classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                const dropdowns = document.querySelectorAll('.dropdown-menu');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });

        // Auto-hide alert cards after 4.5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-card');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 400);
                }, 4500);
            });
        });
    </script>
    <style>
        .alert-card {
            background: var(--bg-card);
            border-left: 4px solid;
            padding: 1.25rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transition: all 0.4s ease;
        }
        .alert-card.alert-success {
            border-left-color: var(--success);
            color: var(--success);
        }
        .alert-card.alert-danger {
            border-left-color: var(--danger);
            color: var(--danger);
        }
    </style>
    @stack('scripts')
</body>
</html>
