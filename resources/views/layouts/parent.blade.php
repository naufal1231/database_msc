<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Portal Orang Tua MSC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom-style.css') }}">
    @yield('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- ─── SIDEBAR ORANG TUA ─── -->
        <aside class="sidebar" id="appSidebar">
            <a href="/" class="sidebar-logo">
                <img src="{{ asset('images/logo-msc.png') }}" alt="MSC" class="sidebar-logo-img">
                <div class="sidebar-logo-text">
                    <span class="club-title">MSC Portal</span>
                    <span class="club-subtitle">Orang Tua Atlet</span>
                </div>
            </a>

            <nav style="flex: 1;">
                <ul class="nav-menu">
                    <li class="nav-item active">
                        <a href="{{ route('parent.dashboard') }}">
                            <span class="nav-icon">🏠</span>
                            <span class="nav-label">Dashboard Orang Tua</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}">
                            <span class="nav-icon">👤</span>
                            <span class="nav-label">Profil Akun</span>
                        </a>
                    </li>
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
                    <h2 class="topbar-title">@yield('header_title', 'Portal Pemantauan Atlet')</h2>
                </div>
                <div class="user-profile">
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">Orang Tua / Wali</div>
                    </div>
                    <div class="user-avatar" style="background: linear-gradient(135deg, rgba(201,168,76,0.2) 0%, rgba(168,135,58,0.3) 100%); border-color: rgba(201,168,76,0.3); color: #e8c96d;">
                        👪
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

                @yield('content')
            </section>
        </main>
    </div>

    <script>
        (function() {
            var sidebar   = document.getElementById('appSidebar');
            var main      = document.getElementById('mainContent');
            var toggleBtn = document.getElementById('sidebarToggle');
            var overlay   = document.getElementById('sidebarOverlay');
            var STORAGE_KEY = 'msc_sidebar_collapsed';

            var mq = window.matchMedia('(max-width: 768px)');
            function isMobile() { return mq.matches; }

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

            if (isMobile()) {
                cleanupMobileState();
                sidebar.classList.remove('collapsed');
                main.classList.remove('sidebar-collapsed');
            } else {
                applyDesktopState();
            }

            toggleBtn.addEventListener('click', function() {
                if (isMobile()) {
                    sidebar.classList.contains('mobile-open') ? closeMobileSidebar() : openMobileSidebar();
                } else {
                    var nowCollapsed = sidebar.classList.toggle('collapsed');
                    main.classList.toggle('sidebar-collapsed', nowCollapsed);
                    localStorage.setItem(STORAGE_KEY, nowCollapsed ? 'true' : 'false');
                }
            });

            overlay.addEventListener('click', closeMobileSidebar);

            sidebar.querySelectorAll('.nav-item a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (isMobile()) closeMobileSidebar();
                });
            });

            var touchStartX = 0;
            sidebar.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
            }, { passive: true });
            sidebar.addEventListener('touchend', function(e) {
                if (e.changedTouches[0].clientX - touchStartX < -60 && isMobile()) {
                    closeMobileSidebar();
                }
            }, { passive: true });

            mq.addEventListener('change', function(e) {
                cleanupMobileState();
                if (e.matches) {
                    sidebar.classList.remove('collapsed');
                    main.classList.remove('sidebar-collapsed');
                } else {
                    applyDesktopState();
                }
            });
        })();
    </script>


    @yield('scripts')
</body>
</html>
