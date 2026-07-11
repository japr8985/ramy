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
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }

        body {
            background-color: #f7f9fb;
            font-family: 'Inter', sans-serif;
        }

        /* Custom elevation for the login card */
        .login-card-elevation {
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .focused-ring:focus-within {
            box-shadow: 0 0 0 2px #4648d4;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-surface text-on-surface min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="absolute inset-0 z-0 pointer-events-none opacity-40">
        <div
            class="absolute top-[-10%] left-[-5%] w-[40%] h-[40%] bg-secondary-fixed opacity-30 blur-[120px] rounded-full">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-5%] w-[40%] h-[40%] bg-primary-fixed opacity-30 blur-[120px] rounded-full">
        </div>
    </div>
    <main class="relative z-10 w-full max-w-[440px] px-margin-mobile md:px-0">
        {{ $slot }}

        <footer class="mt-8 text-center space-y-4">
            <p class="font-body-sm text-body-sm text-on-surface-variant">
                Don't have an account?
                <a class="text-secondary font-semibold hover:underline" href="#">Request Access</a>
            </p>
            <div class="flex items-center justify-center gap-6">
                <a class="font-label-caps text-[10px] text-outline uppercase hover:text-on-surface-variant transition-colors"
                    href="#">Privacy Policy</a>
                <a class="font-label-caps text-[10px] text-outline uppercase hover:text-on-surface-variant transition-colors"
                    href="#">Terms of Service</a>
                <a class="font-label-caps text-[10px] text-outline uppercase hover:text-on-surface-variant transition-colors"
                    href="#">Help Center</a>
            </div>
        </footer>
    </main>
    <div
        class="hidden xl:block absolute right-gutter top-1/2 -translate-y-1/2 w-96 h-[500px] bg-surface-container border border-outline-variant rounded-2xl overflow-hidden opacity-50 grayscale hover:grayscale-0 transition-all duration-700 login-card-elevation">
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="h-4 w-24 bg-outline-variant/30 rounded"></div>
                <div class="h-8 w-8 bg-outline-variant/20 rounded-full"></div>
            </div>
            <div class="space-y-2">
                <div class="h-3 w-full bg-outline-variant/10 rounded"></div>
                <div class="h-3 w-3/4 bg-outline-variant/10 rounded"></div>
            </div>
            <div class="pt-4 grid grid-cols-2 gap-4">
                <div class="h-20 bg-secondary/10 rounded-lg"></div>
                <div class="h-20 bg-primary/10 rounded-lg"></div>
            </div>
            <div class="h-40 bg-surface-container-highest rounded-lg flex items-end p-4 gap-2">
                <div class="flex-1 bg-secondary/20 h-[40%] rounded-t"></div>
                <div class="flex-1 bg-secondary/30 h-[60%] rounded-t"></div>
                <div class="flex-1 bg-secondary/40 h-[85%] rounded-t"></div>
                <div class="flex-1 bg-secondary/20 h-[30%] rounded-t"></div>
                <div class="flex-1 bg-secondary/50 h-[50%] rounded-t"></div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-surface via-transparent to-transparent"></div>
    </div>

    @livewireScripts
</body>

</html>