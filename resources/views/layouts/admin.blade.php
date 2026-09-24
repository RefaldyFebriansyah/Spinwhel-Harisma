<!DOCTYPE html>
<html lang="id" class="h-full bg-[#08090D]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Spinwheel Sanggar Seni Harisma</title>

    <!-- Google Fonts: Plus Jakarta Sans (UI) & Cinzel (Ethnic Serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js & Lucide Icons -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, .font-serif-ethnic { font-family: 'Cinzel', serif; }
        
        /* Custom scrollbar for modern SaaS feel */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0D0E15; }
        ::-webkit-scrollbar-thumb { background: #272738; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3B3C54; }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-[#08090D] text-zinc-100 antialiased flex" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/80 backdrop-blur-sm z-40 lg:hidden"></div>

    <!-- Admin Sidebar Navigation (Sleek Industry Dark SaaS Sidebar) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0E1017] border-r border-zinc-800/80 transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col justify-between shadow-2xl">
        <div>
            <!-- Sidebar Header / Brand Logo -->
            <div class="h-16 flex items-center px-5 border-b border-zinc-800/80 bg-[#0A0B10]">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-harisma.png') }}" alt="Logo Harisma" class="w-9 h-9 object-contain drop-shadow-[0_0_8px_rgba(212,175,55,0.4)]">
                    <div>
                        <h1 class="text-xs font-black font-serif-ethnic text-amber-200 tracking-wider">HARISMA ADMIN</h1>
                        <p class="text-[9px] text-zinc-400 font-bold tracking-widest uppercase">SMKN 1 CIAMIS</p>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="px-3 py-4">
                <p class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-zinc-500 mb-2">Sistem Navigasi</p>
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/10 text-amber-300 font-bold border-l-2 border-amber-400' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-amber-400"></i>
                        <span>Dashboard Overview</span>
                    </a>

                    <a href="{{ route('admin.items.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition duration-150 {{ request()->routeIs('admin.items.*') ? 'bg-amber-500/10 text-amber-300 font-bold border-l-2 border-amber-400' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100' }}">
                        <i data-lucide="users" class="w-4 h-4 text-amber-400"></i>
                        <span>Manajemen Peserta</span>
                    </a>

                    <a href="{{ route('admin.history.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition duration-150 {{ request()->routeIs('admin.history.*') ? 'bg-amber-500/10 text-amber-300 font-bold border-l-2 border-amber-400' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100' }}">
                        <i data-lucide="history" class="w-4 h-4 text-amber-400"></i>
                        <span>Riwayat & Ekspor Log</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition duration-150 {{ request()->routeIs('admin.settings.*') ? 'bg-amber-500/10 text-amber-300 font-bold border-l-2 border-amber-400' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100' }}">
                        <i data-lucide="settings" class="w-4 h-4 text-amber-400"></i>
                        <span>Pengaturan Mesin</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Sidebar Footer / Stage Quick Launch Button -->
        <div class="p-4 border-t border-zinc-800/80 bg-[#0A0B10] space-y-3">
            <a href="{{ route('stage') }}" target="_blank" 
               class="flex items-center justify-center space-x-2 w-full py-2.5 px-3 rounded-lg bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black text-xs transition shadow-md">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>Buka Layar Panggung</span>
            </a>
            <div class="flex items-center justify-between text-[10px] text-zinc-500 px-1">
                <span class="flex items-center space-x-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Server Active</span>
                </span>
                <span>v2.4 &bull; 2026</span>
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top Header Bar -->
        <header class="h-16 bg-[#0E1017] border-b border-zinc-800/80 px-6 flex items-center justify-between shadow-sm z-30">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg bg-zinc-800/60 text-amber-400 lg:hidden hover:bg-zinc-800 transition">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h2 class="text-sm font-bold font-serif-ethnic text-amber-200">@yield('page_title', 'Dashboard Overview')</h2>
                    <p class="text-[11px] text-zinc-400">@yield('page_subtitle', 'Panel Kontrol Interaktif Sanggar Seni Harisma')</p>
                </div>
            </div>

            <!-- Admin Profile Badge -->
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2.5 bg-[#141622] px-3 py-1.5 rounded-lg border border-zinc-800 text-xs">
                    <div class="w-5 h-5 rounded bg-amber-500/20 flex items-center justify-center border border-amber-500/40 text-amber-300 font-bold text-[10px]">
                        P
                    </div>
                    <span class="font-semibold text-zinc-200">Panitia / Admin</span>
                </div>
            </div>
        </header>

        <!-- Dynamic Main Content Scrollable Area -->
        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            <!-- Global Notifications -->
            @if(session('success'))
                <div class="p-3.5 rounded-lg bg-emerald-950/60 border border-emerald-500/40 text-emerald-200 flex items-center space-x-3 shadow-md text-xs">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 flex-shrink-0"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 rounded-lg bg-rose-950/60 border border-rose-500/40 text-rose-200 flex items-center space-x-3 shadow-md text-xs">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400 flex-shrink-0"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-lg bg-rose-950/60 border border-rose-500/40 text-rose-200 space-y-1 shadow-md text-xs">
                    <div class="flex items-center space-x-2 font-bold text-rose-300">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400 flex-shrink-0"></i>
                        <span>Terjadi kesalahan pengisian formulir:</span>
                    </div>
                    <ul class="list-disc list-inside pl-5 space-y-0.5 text-zinc-300">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Lucide Icons Initializer -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
