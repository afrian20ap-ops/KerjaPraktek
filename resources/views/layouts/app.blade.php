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
                    <img src="{{ asset('images/garuda.png') }}" alt="Logo" style="height: 32px; width: auto; object-fit: contain;">
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
                <form action="{{ route('logout') }}" method="POST" style="margin-left:auto; display:inline;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1rem; transition:color 0.2s;" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
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
                    <!-- Notifications Dropdown -->
                    <div class="dropdown" style="position: relative;">
                        <button class="icon-btn" title="Notifikasi" aria-label="Notifikasi" onclick="toggleDropdown('notifMenu', event)">
                            <i class="fa-solid fa-bell"></i>
                            <span class="badge badge-danger" style="position:absolute; top:-2px; right:-2px; font-size:0.6rem; padding:0.15rem 0.35rem; min-width: 18px; height: 18px; display:flex; align-items:center; justify-content:center;">3</span>
                        </button>
                        <div class="dropdown-menu" id="notifMenu">
                            <div class="dropdown-header">
                                <span>Notifikasi</span>
                                <span class="badge badge-primary">3 Baru</span>
                            </div>
                            <div class="dropdown-content">
                                <!-- Dynamic notifications based on role -->
                                @if(session('user_role') == 'admin')
                                    <a href="#" class="dropdown-item">
                                        <div class="item-icon" style="color: var(--primary-500); background: var(--primary-50);"><i class="fa-solid fa-user-plus"></i></div>
                                        <div class="item-info">
                                            <div class="item-title">Karyawan Baru Terdaftar</div>
                                            <div class="item-time">5 menit yang lalu</div>
                                        </div>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <div class="item-icon" style="color: var(--warning); background: color-mix(in srgb, var(--warning) 15%, transparent);"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                                        <div class="item-info">
                                            <div class="item-title">Gaji bulan ini belum disetujui</div>
                                            <div class="item-time">1 jam yang lalu</div>
                                        </div>
                                    </a>
                                @elseif(session('user_role') == 'supervisi')
                                    <a href="#" class="dropdown-item">
                                        <div class="item-icon" style="color: var(--warning); background: color-mix(in srgb, var(--warning) 15%, transparent);"><i class="fa-solid fa-clipboard-check"></i></div>
                                        <div class="item-info">
                                            <div class="item-title">Ada 5 laporan butuh review</div>
                                            <div class="item-time">30 menit yang lalu</div>
                                        </div>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <div class="item-icon" style="color: var(--danger); background: color-mix(in srgb, var(--danger) 15%, transparent);"><i class="fa-solid fa-user-xmark"></i></div>
                                        <div class="item-info">
                                            <div class="item-title">2 Karyawan terlambat hari ini</div>
                                            <div class="item-time">2 jam yang lalu</div>
                                        </div>
                                    </a>
                                @else
                                    <a href="#" class="dropdown-item">
                                        <div class="item-icon" style="color: var(--success); background: color-mix(in srgb, var(--success) 15%, transparent);"><i class="fa-solid fa-money-check-dollar"></i></div>
                                        <div class="item-info">
                                            <div class="item-title">Slip gaji sudah tersedia</div>
                                            <div class="item-time">1 hari yang lalu</div>
                                        </div>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <div class="item-icon" style="color: var(--info); background: color-mix(in srgb, var(--info) 15%, transparent);"><i class="fa-solid fa-bullhorn"></i></div>
                                        <div class="item-info">
                                            <div class="item-title">Pengumuman: Libur Nasional</div>
                                            <div class="item-time">3 hari yang lalu</div>
                                        </div>
                                    </a>
                                @endif
                                <a href="#" class="dropdown-item">
                                    <div class="item-icon" style="color: var(--text-secondary); background: var(--bg-hover);"><i class="fa-solid fa-check-circle"></i></div>
                                    <div class="item-info">
                                        <div class="item-title">Sistem berhasil diupdate</div>
                                        <div class="item-time">1 minggu yang lalu</div>
                                    </div>
                                </a>
                            </div>
                            <div class="dropdown-footer">
                                <a href="#">Lihat semua notifikasi</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown" style="position: relative;">
                        <div class="avatar" style="cursor:pointer;font-size:0.85rem;" title="{{ session('user_name', 'Pengguna') }}" onclick="toggleDropdown('profileMenu', event)">
                            {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                        </div>
                        <div class="dropdown-menu profile-menu" id="profileMenu">
                            <div class="dropdown-header profile-header">
                                <div class="avatar" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                                </div>
                                <div class="profile-info">
                                    <div class="user-name" style="font-size:1rem; font-weight:700; color:var(--text-primary);">{{ session('user_name', 'Pengguna') }}</div>
                                    <div class="user-role" style="font-size:0.8rem; color:var(--text-secondary);">{{ ucfirst(session('user_role', 'guest')) }}</div>
                                </div>
                            </div>
                            <div class="dropdown-content">
                                <a href="{{ route('profile') }}" class="dropdown-item">
                                    <i class="fa-regular fa-user"></i> Profil Saya
                                </a>
                                <a href="{{ route('settings') }}" class="dropdown-item">
                                    <i class="fa-solid fa-gear"></i> Pengaturan
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
    </script>
    @stack('scripts')
</body>
</html>
