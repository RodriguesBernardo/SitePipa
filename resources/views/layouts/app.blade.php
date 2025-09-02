<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PIPA IFRS - @yield('title')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    
    <!-- Custom CSS -->
    <style>
        :root {
            --pipa-red: #b82424;
            --pipa-red-dark: #9a1d1d;
            --pipa-green: #087c04;
            --pipa-green-dark: #066903;
            
            --primary-color: #b82424;
            --primary-light: #d86a6a;
            --secondary-color: #087c04;
            --dark-color: #2b2b2b;
            --dark-light: #3d3d3d;
            --light-color: #f8f9fa;
            --text-color: #2b2b2b;
            --navbar-bg: #ffffff;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            
            --gradient-primary: linear-gradient(135deg, #b82424 0%, #d86a6a 100%);
            --gradient-secondary: linear-gradient(135deg, #087c04 0%, #4CAF50 100%);
            
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --header-height: 64px;
            
            --transition-base: all 0.2s ease-in-out;
        }
        
        [data-bs-theme="dark"] {
            --pipa-red: #d86a6a;
            --pipa-red-dark: #b82424;
            --pipa-green: #4CAF50;
            --pipa-green-dark: #087c04;
            
            --dark-color: #f8f9fa;
            --dark-light: #e0e0e0;
            --light-color: #2b2b2b;
            --text-color: #f8f9fa;
            --navbar-bg: #1a1a1a;
            --sidebar-bg: #1a1a1a;
            --card-bg: #2b2b2b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .badge {
            --bs-badge-padding-x: 0.65em;
            --bs-badge-padding-y: 0.35em;
            --bs-badge-font-size: 0.75em;
            --bs-badge-font-weight: 700;
            /* --bs-badge-color: #fff; */
            --bs-badge-border-radius: var(--bs-border-radius);
            display: inline-block;
            padding: var(--bs-badge-padding-y) var(--bs-badge-padding-x);
            font-size: var(--bs-badge-font-size);
            font-weight: var(--bs-badge-font-weight);
            line-height: 1;
            color: var(--dark-color);
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: var(--bs-badge-border-radius);
        }

        /* Header Styles */
        .app-header {
            height: var(--header-height);
            background-color: var(--navbar-bg);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition-base);
        }
        
        .header-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-color);
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .brand-logo {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border-radius: 8px;
        }
        
        .brand-text {
            transition: var(--transition-base);
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Sidebar Styles */
        .app-sidebar {
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            position: fixed;
            top: var(--header-height);
            left: 0;
            background-color: var(--sidebar-bg);
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition-base);
            overflow-y: auto;
            z-index: 1020;
            padding: 1rem 0;
        }
        
        .sidebar-collapsed .app-sidebar {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-collapsed .brand-text,
        .sidebar-collapsed .nav-link-text {
            display: none;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
            padding: 0 0.75rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 8px;
            color: var(--dark-light);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-base);
        }
        
        .nav-link:hover {
            background-color: rgba(184, 36, 36, 0.08);
            color: var(--primary-color);
        }
        
        .nav-link.active {
            background-color: rgba(184, 36, 36, 0.08);
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .nav-link i {
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .app-main {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 1.5rem;
            transition: var(--transition-base);
            min-height: calc(100vh - var(--header-height));
        }
        
        .sidebar-collapsed .app-main {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
            margin: 0;
            position: relative;
            display: inline-block;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0.5rem 0;
            margin-bottom: 1rem;
        }
        
        .breadcrumb-item a {
            color: var(--dark-light);
            text-decoration: none;
            transition: var(--transition-base);
        }
        
        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }
        
        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            background-color: var(--card-bg);
            margin-bottom: 1.5rem;
            transition: var(--transition-base);
            position: relative;
            overflow: hidden;
        }
        
        .card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: var(--transition-base);
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .btn-pipa-red {
            background: var(--gradient-primary);
            color: white;
        }
        
        .btn-pipa-red:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(184, 36, 36, 0.25);
            color: white;
        }
        
        .btn-pipa-green {
            background: var(--gradient-secondary);
            color: white;
        }
        
        .btn-pipa-green:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(8, 124, 4, 0.25);
            color: white;
        }
        
        /* Toggle Button */
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--dark-light);
            font-size: 1.25rem;
            cursor: pointer;
            transition: var(--transition-base);
            padding: 0.5rem;
            border-radius: 8px;
        }
        
        .sidebar-toggle:hover {
            background-color: rgba(184, 36, 36, 0.08);
            color: var(--primary-color);
        }
        
        /* User Dropdown */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .user-name {
            font-weight: 500;
            color: var(--text-color);
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--dark-light);
        }
        
        /* Theme Toggle */
        .theme-toggle {
            background: none;
            border: none;
            color: var(--dark-light);
            font-size: 1.25rem;
            cursor: pointer;
            transition: var(--transition-base);
            padding: 0.5rem;
            border-radius: 8px;
        }
        
        .theme-toggle:hover {
            background-color: rgba(184, 36, 36, 0.08);
            color: var(--primary-color);
        }
        
        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .app-sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar-mobile-show .app-sidebar {
                transform: translateX(0);
            }
            
            .app-main {
                margin-left: 0;
            }
            
            .sidebar-collapsed .app-main {
                margin-left: 0;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <div class="d-flex align-items-center">
            <!-- Mobile Sidebar Toggle -->
            <button class="sidebar-toggle me-3 d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="header-brand me-4">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="PIPA IFRS Logo" class="img-fluid" style="max-height: 40px;">
                </div>
                <span class="brand-text">PIPA IFRS</span>
            </a>
            
            <!-- Desktop Sidebar Toggle -->
            <button class="sidebar-toggle me-3 d-none d-lg-block">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
        
        <div class="d-flex align-items-center ms-auto">
            <!-- Theme Toggle -->
            <button class="theme-toggle me-2">
                <i class="fas fa-moon"></i>
            </button>
            
            @auth
            <!-- User Dropdown -->
            <div class="dropdown">
                <div class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="d-none d-lg-block">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">
                            @if(Auth::user()->is_admin)
                                Administrador
                            @elseif(!empty(Auth::user()->permissions))
                                Bolsista
                                
                            @else
                                Usuário
                            @endif
                        </div>
                    </div>
                </div>
                
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    @if(auth()->user()->is_admin || !empty(auth()->user()->permissions))
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-cog me-2"></i> Painel Admin
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @endif
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Sair
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
            
            @guest
            <a class="btn btn-pipa-red" href="{{ route('login') }}">Login</a>
            @endguest
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="app-sidebar">
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span class="nav-link-text">Início</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('games.index') }}" class="nav-link {{ Request::is('games*') && !Request::is('admin/games*') ? 'active' : '' }}">
                    <i class="fas fa-gamepad"></i>
                    <span class="nav-link-text">Jogos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('news.index') }}" class="nav-link {{ Request::is('news*') && !Request::is('admin/news*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i>
                    <span class="nav-link-text">Notícias</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('help.index') }}" class="nav-link {{ Request::is('help*') && !Request::is('admin/help*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span class="nav-link-text">Ajuda</span>
                </a>
            </li>
            
            @auth
                @if(auth()->user()->is_admin || !empty(auth()->user()->permissions))
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-link-text">Dashboard Administrativo</span>
                        </a>
                    </li>
                    
                    @if(auth()->user()->is_admin || auth()->user()->hasPermission('edit_games') || auth()->user()->hasPermission('create_games'))
                    <li class="nav-item">
                        <a href="{{ route('admin.games.index') }}" class="nav-link {{ Request::is('admin/games*') ? 'active' : '' }}">
                            <i class="fas fa-gamepad"></i>
                            <span class="nav-link-text">Gerenciar Jogos</span>
                        </a>
                    </li>
                    @endif
                    
                    @if(auth()->user()->is_admin || auth()->user()->hasPermission('edit_news') || auth()->user()->hasPermission('create_news'))
                    <li class="nav-item">
                        <a href="{{ route('admin.news.index') }}" class="nav-link {{ Request::is('admin/news*') ? 'active' : '' }}">
                            <i class="fas fa-newspaper"></i>
                            <span class="nav-link-text">Gerenciar Notícias</span>
                        </a>
                    </li>
                    @endif
                    
                    @if(auth()->user()->is_admin || auth()->user()->hasPermission('edit_help'))
                    <li class="nav-item">
                        <a href="{{ route('admin.help.edit') }}" class="nav-link {{ Request::is('admin/help*') ? 'active' : '' }}">
                            <i class="fas fa-question-circle"></i>
                            <span class="nav-link-text">Gerenciar Ajuda</span>
                        </a>
                    </li>
                    @endif
                    
                    @if(auth()->user()->is_admin)
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span class="nav-link-text">Gerenciar Usuários</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->is_admin)
                    <li class="nav-item">
                        <a href="{{ route('admin.logs.index') }}" class="nav-link {{ Request::is('admin/logs*') ? 'active' : '' }}">
                            <i class="fas fa-history"></i>
                            <span class="nav-link-text">Logs de Atividade</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->is_admin || auth()->user()->hasPermission('view_calendar'))
                    <li class="nav-item">
                        <a href="{{ route('admin.calendar.index') }}" class="nav-link {{ Request::is('admin/calendar*') ? 'active' : '' }}">
                            <i class="fas fa-calendar"></i>
                            <span class="nav-link-text">Calendário</span>
                        </a>
                    </li>
                    @endif
                @endif
            @endauth
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="app-main">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">@yield('title')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            @hasSection('breadcrumb')
                                @yield('breadcrumb')
                            @else
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
                                <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                            @endif
                        </ol>
                    </nav>
                </div>
                <div>
                    @yield('header-actions')
                </div>
            </div>
            
            <!-- Notifications -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Content -->
            <div class="fade-in">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.tiny.cloud/1/97zm89qm6yo9smrpqxxbnztkt2x3txvoyyyitsu0nkv662ds/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

    
    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('.sidebar-toggle').click(function() {
                $('body').toggleClass('sidebar-collapsed');
                
                // Change icon
                $(this).find('i').toggleClass('fa-chevron-left fa-chevron-right');
                
                // Save state
                localStorage.setItem('sidebarCollapsed', $('body').hasClass('sidebar-collapsed'));
            });
            
            // Mobile sidebar toggle
            $('.sidebar-toggle.me-3.d-lg-none').click(function() {
                $('body').toggleClass('sidebar-mobile-show');
            });
            
            // Check initial sidebar state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                $('body').addClass('sidebar-collapsed');
                $('.sidebar-toggle i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
            }
            
            // Theme toggle
            $('.theme-toggle').click(function() {
                const html = $('html');
                const isDark = html.attr('data-bs-theme') === 'dark';
                
                // Toggle theme
                html.attr('data-bs-theme', isDark ? 'light' : 'dark');
                
                // Change icon
                $(this).find('i').toggleClass('fa-moon fa-sun');
                
                // Save preference
                localStorage.setItem('darkMode', !isDark);
            });
            
            // Check theme preference
            if (localStorage.getItem('darkMode') === 'true') {
                $('html').attr('data-bs-theme', 'dark');
                $('.theme-toggle i').removeClass('fa-moon').addClass('fa-sun');
            }
            
            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches && 
                !localStorage.getItem('darkMode')) {
                $('html').attr('data-bs-theme', 'dark');
                $('.theme-toggle i').removeClass('fa-moon').addClass('fa-sun');
                localStorage.setItem('darkMode', 'true');
            }
            
            // Close mobile sidebar when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest('.app-sidebar').length && 
                    !$(event.target).closest('.sidebar-toggle.me-3.d-lg-none').length && 
                    $('body').hasClass('sidebar-mobile-show')) {
                    $('body').removeClass('sidebar-mobile-show');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>