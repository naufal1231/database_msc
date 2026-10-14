<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - MSC Database</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom-style.css') }}">
    @yield('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- ─── SIDEBAR ─── -->
        <aside class="sidebar" id="appSidebar">
            <a href="/" class="sidebar-logo">
                <img src="{{ asset('images/logo-msc.png') }}" alt="MSC" class="sidebar-logo-img">
                <div class="sidebar-logo-text">
                    <span class="club-title">Marabunta</span>
                    <span class="club-subtitle">Swimming Club</span>
                </div>
            </a>

            <nav style="flex: 1;">
                <ul class="nav-menu">
                    <li class="nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.dashboard') : route('admin.dashboard') }}">
                            <span class="nav-icon">🏠</span>
                            <span class="nav-label">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('athletes.*') ? 'active' : '' }}">
                        <a href="{{ route('athletes.index') }}">
                            <span class="nav-icon">👥</span>
                            <span class="nav-label">Data Atlet</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('competitions.*') ? 'active' : '' }}">
                        <a href="{{ route('competitions.index') }}">
                            <span class="nav-icon">🏆</span>
                            <span class="nav-label">Perlombaan</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                        <a href="{{ route('attendance.index') }}">
                            <span class="nav-icon">📅</span>
                            <span class="nav-label">Absensi</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="{{ route('reports.index') }}">
                            <span class="nav-icon">📊</span>
                            <span class="nav-label">Laporan Kemajuan</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('event-history.*') ? 'active' : '' }}">
                        <a href="{{ route('event-history.index') }}">
                            <span class="nav-icon">📊</span>
                            <span class="nav-label">riwayat perlombaan</span>
                        </a>
                    </li>
                    @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
                        <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <a href="{{ route('settings.index') }}">
                                <span class="nav-icon">⚙️</span>
                                <span class="nav-label">Pengaturan Sistem</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn">
                        <span class="nav-icon">🚪</span>
                        <span class="btn-label">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ─── MOBILE OVERLAY ─── -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- ─── MAIN CONTENT ─── -->
        <main class="main-content" id="mainContent">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
                        ☰
                    </button>
                    <h2 class="topbar-title">@yield('header_title', 'Database Atlet')</h2>
                </div>
                <div class="user-profile">
                    <div class="user-info topbar-user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">
                            {{ auth()->user()->role === 'super_admin' ? 'Pelatih Kepala' : 'Pelatih Kelas' }}
                        </div>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <section class="content-body">
                @if (session('success'))
                    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                        ✕ {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                        <ul style="list-style-type: none; margin: 0; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <script>
        (function() {
            var sidebar  = document.getElementById('appSidebar');
            var main     = document.getElementById('mainContent');
            var toggleBtn = document.getElementById('sidebarToggle');
            var overlay  = document.getElementById('sidebarOverlay');
            var STORAGE_KEY = 'msc_sidebar_collapsed';

            // Reliable mobile detection using matchMedia
            var mq = window.matchMedia('(max-width: 768px)');

            function isMobile() {
                return mq.matches;
            }

            /* ── MOBILE ── */
            function openMobileSidebar() {
                sidebar.classList.add('mobile-open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            /* ── DESKTOP ── */
            function applyDesktopState() {
                var collapsed = localStorage.getItem(STORAGE_KEY) === 'true';
                sidebar.classList.toggle('collapsed', collapsed);
                main.classList.toggle('sidebar-collapsed', collapsed);
            }

            function cleanupMobileState() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            // Initial state on page load
            if (isMobile()) {
                // Ensure sidebar is hidden off-screen on mobile load
                cleanupMobileState();
                sidebar.classList.remove('collapsed');
                main.classList.remove('sidebar-collapsed');
            } else {
                applyDesktopState();
            }

            // Toggle button click
            toggleBtn.addEventListener('click', function() {
                if (isMobile()) {
                    if (sidebar.classList.contains('mobile-open')) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                } else {
                    var nowCollapsed = sidebar.classList.toggle('collapsed');
                    main.classList.toggle('sidebar-collapsed', nowCollapsed);
                    localStorage.setItem(STORAGE_KEY, nowCollapsed ? 'true' : 'false');
                }
            });

            // Overlay click closes sidebar
            overlay.addEventListener('click', closeMobileSidebar);

            // Nav link click closes sidebar on mobile (allows navigation)
            sidebar.querySelectorAll('.nav-item a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (isMobile()) {
                        closeMobileSidebar();
                    }
                });
            });

            // Swipe left on sidebar to close (touch support)
            var touchStartX = 0;
            sidebar.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
            }, { passive: true });
            sidebar.addEventListener('touchend', function(e) {
                var dx = e.changedTouches[0].clientX - touchStartX;
                if (dx < -60 && isMobile()) { // swipe left >= 60px
                    closeMobileSidebar();
                }
            }, { passive: true });

            // On resize: sync state between mobile/desktop
            mq.addEventListener('change', function(e) {
                if (e.matches) {
                    // Switched to mobile
                    cleanupMobileState();
                    sidebar.classList.remove('collapsed');
                    main.classList.remove('sidebar-collapsed');
                } else {
                    // Switched to desktop
                    cleanupMobileState();
                    applyDesktopState();
                }
            });
        })();
    </script>


    @yield('scripts')
</body>
</html>
