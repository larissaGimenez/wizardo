<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Wizardo')</title>

    <!-- Google Fonts for Harry Potter Theme -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Spectral:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <style>
        /* Sistema de Temas - Harry Potter Edition */
        :root {
            --bg-color: #f5ecd7; /* Parchment */
            --text-color: #2c1b0a; /* Dark Ink */
            --card-bg: #fcf8ed; /* Light Parchment */
            --border-color: #c4b494; /* Aged Gold/Brown */
            --sidebar-bg: #eadecc; /* Darker Parchment */
            --sidebar-text: #5c3a21; /* Brown */
            --sidebar-active-bg: #d9cbb0;
            --sidebar-active-text: #2c1b0a;
            --modal-bg: #fcf8ed;
            --input-bg: #ffffff;
            --input-border: #bca07c;
            --topbar-bg: #eadecc;
            --accent-color: #741b1b; /* Gryffindor Red */
            --accent-hover: #5a1515;
            --gold-color: #d4af37;
        }

        [data-theme="dark"] {
            --bg-color: #0c0a0f; /* Night Sky over Hogwarts */
            --text-color: #e2dcd0; /* Parchment Text */
            --card-bg: #1a1622; /* Dark Stone */
            --border-color: #4a3e2e; /* Dark Gold/Brown */
            --sidebar-bg: #15121b;
            --sidebar-text: #a69a8a;
            --sidebar-active-bg: #2c2538;
            --sidebar-active-text: #f5efe6;
            --modal-bg: #1a1622;
            --input-bg: #252030;
            --input-border: #4a3e2e;
            --topbar-bg: #15121b;
            --accent-color: #d4af37; /* Gold */
            --accent-hover: #bda030;
            --gold-color: #d4af37;
        }

        body {
            font-family: 'Spectral', serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }

        /* Custom Scrollbar - Magical Theme */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-color);
            border-left: 1px solid var(--border-color);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 10px;
            border: 3px solid var(--bg-color);
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-hover);
            border-width: 2px;
        }

        /* Firefox support */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--accent-color) var(--bg-color);
        }

        h1, h2, h3, h4, h5, h6, .logo, .nav-item .label {
            font-family: 'Cinzel', serif;
            letter-spacing: 0.05em;
        }

        /* Aplicação das variáveis nos componentes globais */
        .sidebar {
            background-color: var(--sidebar-bg) !important;
            border-right: 2px solid var(--border-color) !important;
            display: flex;
            flex-direction: column;
        }

        .logo {
            color: var(--accent-color) !important;
            font-size: 1.5rem;
            font-weight: 900;
            text-shadow: 1px 1px 0px var(--gold-color);
        }

        .nav-links {
            flex-grow: 1;
        }

        .nav-item {
            color: var(--sidebar-text) !important;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-item:hover, .nav-item.active {
            background-color: var(--sidebar-active-bg) !important;
            color: var(--sidebar-active-text) !important;
            box-shadow: inset 4px 0 0 var(--accent-color);
        }

        .nav-item .icon svg {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .nav-item:hover .icon svg {
            transform: scale(1.18) rotate(8deg);
        }

        .nav-item.active .icon svg {
            transform: scale(1.1);
        }

        .main-content {
            background-color: var(--bg-color);
        }

        .content-area {
            background-color: var(--bg-color) !important;
            color: var(--text-color) !important;
        }

        .top-bar {
            background-color: var(--topbar-bg) !important;
            border-bottom: 2px solid var(--border-color) !important;
            color: var(--text-color) !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .theme-toggle-btn-top {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
            color: var(--text-color);
            padding: 0.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
            margin-left: 0.5rem;
        }

        .theme-toggle-btn-top:hover {
            background-color: var(--sidebar-active-bg);
        }

        /* Adaptações para os managers (Cards e Modais) */
        .spell-card, .wheel-card {
            background-color: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-color) !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
            transition: transform 0.2s, box-shadow 0.2s !important;
        }

        [data-theme="dark"] .spell-card, [data-theme="dark"] .wheel-card {
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.05) !important;
        }

        .spell-card:hover, .wheel-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
            border-color: var(--accent-color) !important;
        }

        .spell-header, .spell-actions, .card-actions {
            background-color: rgba(0, 0, 0, 0.03) !important;
            border-color: var(--border-color) !important;
        }

        .modal-content {
            background-color: var(--modal-bg) !important;
            border: 3px solid var(--border-color) !important;
            color: var(--text-color) !important;
            border-radius: 1rem !important;
        }

        .modal-header {
            background-color: rgba(0, 0, 0, 0.03) !important;
            border-bottom: 2px solid var(--border-color) !important;
        }

        .form-group input, .form-group textarea, .form-group select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--input-border) !important;
            color: var(--text-color) !important;
            border-radius: 0.375rem !important;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 3px rgba(116, 27, 27, 0.1) !important;
        }

        [data-theme="dark"] .form-group input:focus, [data-theme="dark"] .form-group textarea:focus, [data-theme="dark"] .form-group select:focus {
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1) !important;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--accent-color) !important;
            color: white !important;
            font-family: 'Cinzel', serif !important;
            border: 1px solid var(--gold-color) !important;
            transition: all 0.2s !important;
        }

        .btn-primary:hover {
            background-color: var(--accent-hover) !important;
            box-shadow: 0 0 10px var(--gold-color) !important;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: var(--sidebar-bg) !important;
            color: var(--text-color) !important;
            border: 1px solid var(--border-color) !important;
            font-family: 'Cinzel', serif !important;
        }

        .btn-secondary:hover {
            background-color: var(--sidebar-active-bg) !important;
        }

        /* Badges */
        .badge {
            font-family: 'Cinzel', serif;
            font-weight: 600;
        }

        .badge-gain {
            background-color: rgba(21, 128, 61, 0.1) !important;
            color: #15803d !important;
            border: 1px solid #15803d !important;
        }

        .badge-damage {
            background-color: rgba(185, 28, 28, 0.1) !important;
            color: #b91c1c !important;
            border: 1px solid #b91c1c !important;
        }

        .badge-daily {
            background-color: rgba(212, 175, 55, 0.1) !important;
            color: #b45309 !important;
            border: 1px solid #d4af37 !important;
        }

        .badge-dark {
            background-color: rgba(0, 0, 0, 0.2) !important;
            color: var(--text-color) !important;
            border: 1px solid var(--border-color) !important;
        }

        .checkbox-list {
            background-color: var(--bg-color) !important;
            border-color: var(--input-border) !important;
        }

        .checkbox-label {
            color: var(--text-color) !important;
        }

        .empty-state {
            background-color: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-color) !important;
        }

        .filter-group label {
            color: var(--text-color) !important;
        }

        /* Adicionais para o tema Harry Potter */
        .spell-name, .wheel-name {
            font-family: 'Cinzel', serif !important;
            color: var(--text-color) !important;
        }

        .spell-action {
            font-family: 'Spectral', serif !important;
            color: var(--text-color) !important;
            opacity: 0.8;
        }

        .wheel-tag {
            background-color: rgba(0, 0, 0, 0.05) !important;
            color: var(--text-color) !important;
            border: 1px solid var(--border-color) !important;
            font-family: 'Cinzel', serif;
            font-size: 0.7rem !important;
        }

        .spell-image-placeholder {
            background-color: var(--sidebar-bg) !important;
            color: var(--border-color) !important;
        }

        .user-profile span {
            font-family: 'Cinzel', serif !important;
            font-weight: 600;
            color: var(--text-color);
        }
    </style>

    <script>
        // Inicialização do tema antes do corpo carregar para evitar flash de cor branca
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
    </script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">Wizardo</div>
                <button class="hamburger" id="sidebar-toggle" aria-label="Toggle Sidebar">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="{{ route('welcome') }}" class="nav-item {{ request()->routeIs('welcome') ? 'active' : '' }}">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; transition: transform 0.3s ease;">
                                <path d="M2 7h6a4 4 0 0 1 4 4v10a3 3 0 0 0-3-3H2z" />
                                <path d="M22 7h-6a4 4 0 0 0-4 4v10a3 3 0 0 1 3-3h7z" />
                                <path d="M12 2 L13.5 4.5 L16 6 L13.5 7.5 L12 10 L10.5 7.5 L8 6 L10.5 4.5 Z" />
                            </svg>
                        </span>
                        <span class="label">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('wheel.manager') }}" class="nav-item {{ request()->routeIs('wheel.manager') ? 'active' : '' }}">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; transition: transform 0.3s ease;">
                                <circle cx="12" cy="12" r="9" />
                                <circle cx="12" cy="12" r="2" />
                                <line x1="12" y1="3" x2="12" y2="10" />
                                <line x1="12" y1="14" x2="12" y2="21" />
                                <line x1="3" y1="12" x2="10" y2="12" />
                                <line x1="14" y1="12" x2="21" y2="12" />
                                <line x1="5.64" y1="5.64" x2="10.59" y2="10.59" />
                                <line x1="13.41" y1="13.41" x2="18.36" y2="18.36" />
                                <line x1="18.36" y1="5.64" x2="13.41" y2="10.59" />
                                <line x1="10.59" y1="13.41" x2="5.64" y2="18.36" />
                            </svg>
                        </span>
                        <span class="label">Rodas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('spell.manager') }}" class="nav-item {{ request()->routeIs('spell.manager') ? 'active' : '' }}">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; transition: transform 0.3s ease;">
                                <path d="M18.5 5.5L5.5 18.5" />
                                <path d="M15 4l.5 1.5L17 6l-1.5.5L15 8l-.5-1.5L13 6l1.5-.5z" />
                                <path d="M9 2l.3.7.7.3-.7.3-.3.7-.3-.7-.7-.3.7-.3z" />
                                <path d="M20 13l.3.7.7.3-.7.3-.3.7-.3-.7-.7-.3.7-.3z" />
                            </svg>
                        </span>
                        <span class="label">Feitiços</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('quest.manager') }}" class="nav-item {{ request()->routeIs('quest.manager') ? 'active' : '' }}">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; transition: transform 0.3s ease;">
                                <path d="M4 16v-2.38C4 11.5 5.88 9.85 6 7.07l.08-1.57A1.66 1.66 0 0 1 7.72 4h.3a1.66 1.66 0 0 1 1.62 1.5l.08 1.57c.12 2.78 2 4.43 2 6.55V16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z" />
                                <path d="M12 11.5v-2.38c0-2.12 1.88-3.77 2-6.55l.08-1.57A1.66 1.66 0 0 1 15.72 1h.3a1.66 1.66 0 0 1 1.62 1.5l.08 1.57c.12 2.78 2 4.43 2 6.55V11.5a2 2 0 0 1-2 2h-3.7a2 2 0 0 1-2-2z" />
                            </svg>
                        </span>
                        <span class="label">Missões</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('challenge.manager') }}" class="nav-item {{ request()->routeIs('challenge.manager') ? 'active' : '' }}">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; transition: transform 0.3s ease;">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                <circle cx="12" cy="11" r="3" />
                                <path d="M12 8v6" />
                                <path d="M10 11h4" />
                            </svg>
                        </span>
                        <span class="label">Desafios</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; transition: transform 0.3s ease;">
                                <circle cx="8" cy="12" r="4" />
                                <path d="M12 12h8" />
                                <path d="M18 12v3M20 12v3" />
                                <path d="M8 10v4M6 12h4" />
                            </svg>
                        </span>
                        <span class="label">Configurações</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <header class="top-bar">
                <div class="top-left-actions">
                    <button class="mobile-toggle" id="mobile-toggle" aria-label="Open Sidebar">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    
                    <!-- Alternador de Tema na Navbar -->
                    <button onclick="toggleTheme()" class="theme-toggle-btn-top" title="Alternar Tema">
                        <span class="icon">🌓</span>
                    </button>
                </div>
                
                <div class="user-profile">
                    <span>Mago Administrador</span>
                </div>
            </header>

            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Sidebar Script -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>