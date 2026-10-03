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
                    @if(auth()->user()->role === 'super_admin')
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
                    <div class="user-info">
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
            const sidebar = document.getElementById('appSidebar');
            const main = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('sidebarToggle');
            const STORAGE_KEY = 'msc_sidebar_collapsed';

            // Restore state from localStorage
            const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                main.classList.add('sidebar-collapsed');
            }

            toggleBtn.addEventListener('click', function() {
                const nowCollapsed = sidebar.classList.toggle('collapsed');
                main.classList.toggle('sidebar-collapsed', nowCollapsed);
                localStorage.setItem(STORAGE_KEY, nowCollapsed ? 'true' : 'false');
            });
        })();
    </script>

    @yield('scripts')
</body>
</html>
