<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@500&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-tint": "#565e74",
                        "on-secondary": "#ffffff",
                        "on-tertiary-container": "#98805d",
                        "tertiary-fixed": "#fcdeb5",
                        "primary-container": "#131b2e",
                        "secondary": "#4648d4",
                        "on-background": "#191c1e",
                        "on-surface-variant": "#45464d",
                        "on-surface": "#191c1e",
                        "tertiary-fixed-dim": "#dec29a",
                        "error": "#ba1a1a",
                        "on-secondary-fixed": "#07006c",
                        "surface": "#f7f9fb",
                        "surface-dim": "#d8dadc",
                        "on-error": "#ffffff",
                        "outline": "#76777d",
                        "background": "#f7f9fb",
                        "surface-bright": "#f7f9fb",
                        "on-secondary-fixed-variant": "#2f2ebe",
                        "secondary-fixed": "#e1e0ff",
                        "inverse-on-surface": "#eff1f3",
                        "secondary-container": "#6063ee",
                        "on-primary-container": "#7c839b",
                        "tertiary": "#000000",
                        "surface-container": "#eceef0",
                        "primary": "#000000",
                        "on-tertiary": "#ffffff",
                        "primary-fixed-dim": "#bec6e0",
                        "on-primary-fixed-variant": "#3f465c",
                        "error-container": "#ffdad6",
                        "surface-container-high": "#e6e8ea",
                        "surface-container-lowest": "#ffffff",
                        "inverse-surface": "#2d3133",
                        "on-tertiary-fixed-variant": "#574425",
                        "on-tertiary-fixed": "#271901",
                        "on-primary": "#ffffff",
                        "secondary-fixed-dim": "#c0c1ff",
                        "surface-container-low": "#f2f4f6",
                        "tertiary-container": "#271901",
                        "surface-container-highest": "#e0e3e5",
                        "surface-variant": "#e0e3e5",
                        "on-secondary-container": "#fffbff",
                        "outline-variant": "#c6c6cd",
                        "on-primary-fixed": "#131b2e",
                        "inverse-primary": "#bec6e0",
                        "on-error-container": "#93000a",
                        "primary-fixed": "#dae2fd"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "24px",
                        "sidebar-width": "280px",
                        "stack-gap": "12px",
                        "sidebar-collapsed": "80px",
                        "container-max": "1440px",
                        "margin-mobile": "16px"
                    },
                    "fontFamily": {
                        "headline-lg-mobile": ["Inter"],
                        "headline-xl": ["Inter"],
                        "headline-lg": ["Inter"],
                        "body-sm": ["Inter"],
                        "data-mono": ["JetBrains Mono"],
                        "body-md": ["Inter"],
                        "label-caps": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg-mobile": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "headline-xl": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "headline-lg": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "data-mono": ["13px", { "lineHeight": "16px", "fontWeight": "500" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "label-caps": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }]
                    }
                },
            },
        }
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@500&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-tint": "#565e74",
                        "on-secondary": "#ffffff",
                        "on-tertiary-container": "#98805d",
                        "tertiary-fixed": "#fcdeb5",
                        "primary-container": "#131b2e",
                        "secondary": "#4648d4",
                        "on-background": "#191c1e",
                        "on-surface-variant": "#45464d",
                        "on-surface": "#191c1e",
                        "tertiary-fixed-dim": "#dec29a",
                        "error": "#ba1a1a",
                        "on-secondary-fixed": "#07006c",
                        "surface": "#f7f9fb",
                        "surface-dim": "#d8dadc",
                        "on-error": "#ffffff",
                        "outline": "#76777d",
                        "background": "#f7f9fb",
                        "surface-bright": "#f7f9fb",
                        "on-secondary-fixed-variant": "#2f2ebe",
                        "secondary-fixed": "#e1e0ff",
                        "inverse-on-surface": "#eff1f3",
                        "secondary-container": "#6063ee",
                        "on-primary-container": "#7c839b",
                        "tertiary": "#000000",
                        "surface-container": "#eceef0",
                        "primary": "#000000",
                        "on-tertiary": "#ffffff",
                        "primary-fixed-dim": "#bec6e0",
                        "on-primary-fixed-variant": "#3f465c",
                        "error-container": "#ffdad6",
                        "surface-container-high": "#e6e8ea",
                        "surface-container-lowest": "#ffffff",
                        "inverse-surface": "#2d3133",
                        "on-tertiary-fixed-variant": "#574425",
                        "on-tertiary-fixed": "#271901",
                        "on-primary": "#ffffff",
                        "secondary-fixed-dim": "#c0c1ff",
                        "surface-container-low": "#f2f4f6",
                        "tertiary-container": "#271901",
                        "surface-container-highest": "#e0e3e5",
                        "surface-variant": "#e0e3e5",
                        "on-secondary-container": "#fffbff",
                        "outline-variant": "#c6c6cd",
                        "on-primary-fixed": "#131b2e",
                        "inverse-primary": "#bec6e0",
                        "on-error-container": "#93000a",
                        "primary-fixed": "#dae2fd"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "24px",
                        "sidebar-width": "280px",
                        "stack-gap": "12px",
                        "sidebar-collapsed": "80px",
                        "container-max": "1440px",
                        "margin-mobile": "16px"
                    },
                    "fontFamily": {
                        "headline-lg-mobile": ["Inter"],
                        "headline-xl": ["Inter"],
                        "headline-lg": ["Inter"],
                        "body-sm": ["Inter"],
                        "data-mono": ["JetBrains Mono"],
                        "body-md": ["Inter"],
                        "label-caps": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg-mobile": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "headline-xl": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "headline-lg": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "data-mono": ["13px", { "lineHeight": "16px", "fontWeight": "500" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "label-caps": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }]
                    }
                },
            },
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-surface font-body-md text-on-surface">
    <div class="flex min-h-screen">

        <x-layout.sidebar />
        <main class="flex-1 flex flex-col min-w-0">
            <!-- TopNavBar -->
            <header class="sticky top-0 z-40 flex items-center justify-between w-full h-16 px-gutter bg-surface-container-lowest dark:bg-surface-container-low border-b border-outline-variant shadow-sm">
    <!-- Buscador Global -->
    <div class="flex items-center gap-4 flex-1">
        <div class="relative w-full max-w-md"> 
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-secondary text-body-sm"
                placeholder="Search stock, barcodes, or categories..." type="text" />
        </div>
    </div>
    
    <!-- Botones de Acción y Perfil -->
    <div class="flex items-center gap-2 md:gap-4">
        <button class="p-2 hover:bg-surface-container-high rounded-full transition-all text-on-surface-variant">
            <span class="material-symbols-outlined">barcode_scanner</span>
        </button>
        
        <button class="p-2 hover:bg-surface-container-high rounded-full transition-all text-on-surface-variant relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full"></span>
        </button>
        
        <div class="h-6 w-[1px] bg-outline-variant mx-1 hidden md:block"></div>
        
        <!-- DROPDOWN DE PERFIL (AlpineJS) -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" type="button" class="flex items-center gap-2 p-1.5 hover:bg-surface-container-high rounded-xl transition-all focus:ring-2 focus:ring-outline-variant">
                <img class="w-8 h-8 rounded-full object-cover shadow-xs border border-outline-variant"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAuPTDCyLn9GjwQRXhnwarLhfNbAu7or-XxZBFHcASqzbwT_X4jhKbWL1WiQlWexPl5fmHWG_uKKlSsAEiRWYbaiVDHCYWBQb5e8o7aF7qDFljWlWxT4x0SSa9_I_YlAOvDJjVo9BIt1mtkYHtYLbxY7Q4qKBNCJydtB9Bw8xA6gK84v_CI4Cq9J-9px5HiZUB6Vn_Ebj-9R712qN7JOmfFjUYPiAeHO3vrK2kc6xyx6H9iazkQGchJ0Q"
                    alt="User Avatar" />
                <span class="material-symbols-outlined text-outline text-sm hidden md:block" x-text="open ? 'expand_less' : 'expand_more'"></span>
            </button>

            <!-- Menú Desplegable Flotante -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-52 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-xl py-2 z-50 divide-y divide-outline-variant/40"
                 style="display: none;">
                
                <!-- Datos del Usuario Logueado (Informativo) -->
                <div class="px-4 py-2.5">
                    <p class="text-xs font-bold text-primary truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="text-[10px] text-outline uppercase font-semibold tracking-wide truncate">{{ auth()->user()->email ?? 'admin@nexus.com' }}</p>
                </div>

                <!-- Opciones del Menú -->
                <div class="py-1">
                    <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-2 text-body-sm text-primary hover:bg-surface-container transition-colors font-medium">
                        <span class="material-symbols-outlined text-md">settings</span>
                        Configuración
                    </a>
                </div>

                <!-- Botón de Cierre de Sesión -->
                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-body-sm text-error hover:bg-error-container/20 transition-colors font-semibold text-left">
                            <span class="material-symbols-outlined text-md">logout</span>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</header>
            <!-- Main Content Area -->
            <div class="p-gutter overflow-y-auto">
                {{ $slot }}
            </div>
        </main>
        <div x-data="{ open: false, title: '', message: '' }"
            x-on:notify.window="open = true; title = $event.detail.title; message = $event.detail.message; setTimeout(() => open = false, 4000)"
            x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-24 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-24 opacity-0"
            class="fixed bottom-8 right-8 bg-inverse-surface text-inverse-on-surface px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 z-50"
            style="display: none;">
            <span class="material-symbols-outlined text-green-400">check_circle</span>
            <div>
                <p class="font-bold text-sm" x-text="title"></p>
                <p class="text-xs opacity-80" x-text="message"></p>
            </div>
        </div>
    </div>


    @livewireScripts
</body>

</html>