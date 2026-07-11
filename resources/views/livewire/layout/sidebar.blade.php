<div>
    <!-- Aside para Escritorio (Se mantiene idéntico pero limpio) -->
    <aside
        class="hidden md:flex flex-col h-screen sticky left-0 top-0 w-sidebar-width border-r border-outline-variant bg-surface dark:bg-surface-dim shrink-0">
        <div class="p-6">
            <h1 class="text-headline-lg font-headline-lg font-bold text-primary dark:text-on-surface">
                Nexus Commercial
            </h1>
            <p class="text-label-caps font-label-caps text-on-surface-variant opacity-70">
                Management Suite
            </p>
        </div>

        <nav class="flex-1 px-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('dashboard') ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold' : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-caps text-label-caps">Dashboard</span>
            </a>

            <a href="{{ route('products') }}"
                class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('products*') ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold' : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">inventory_2</span>
                <span class="font-label-caps text-label-caps">Inventario</span>
            </a>

            <a href="{{ route('sales') }}"
                class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('sales*') ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold' : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">point_of_sale</span>
                <span class="font-label-caps text-label-caps">Sales (POS)</span>
            </a>

            <a href="{{ route('customers') }}"
                class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('customers*') ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold' : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">group</span>
                <span class="font-label-caps text-label-caps">Clientes</span>
            </a>

            <a href="{{ route('suppliers') }}"
                class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('suppliers*') ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold' : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">local_shipping</span>
                <span class="font-label-caps text-label-caps">Proveedores</span>
            </a>

            <a href="{{ route('accounts-receivable') }}"
                class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('accounts-receivable*') ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold' : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">payments</span>
                <span class="font-label-caps text-label-caps">Cuentas por cobrar</span>
            </a>
            <a href="{{ route('categories') }}"
                class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('categories*') ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold' : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">category</span>
                <span class="font-label-caps text-label-caps">Categorías</span>
            </a>

            <a href="{{ route('settings') }}"
                class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('settings*') ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold' : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-label-caps text-label-caps">Config</span>
            </a>
        </nav>

        <div class="p-6 border-t border-outline-variant flex items-center gap-3">
            <img class="w-10 h-10 rounded-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAuPTDCyLn9GjwQRXhnwarLhfNbAu7or-XxZBFHcASqzbwT_X4jhKbWL1WiQlWexPl5fmHWG_uKKlSsAEiRWYbaiVDHCYWBQb5e8o7aF7qDFljWlWxT4x0SSa9_I_YlAOvDJjVo9BIt1mtkYHtYLbxY7Q4qKBNCJydtB9Bw8xA6gK84v_CI4Cq9J-9px5HiZUB6Vn_Ebj-9R712qN7JOmfFjUYPiAeHO3vrK2kc6xyx6H9iazkQGchJ0Q"
                alt="User Avatar" />
            <div>
                <p class="font-label-caps text-label-caps font-bold">Admin User</p>
                <p class="text-[10px] uppercase text-on-surface-variant">Systems Overseer</p>
            </div>
        </div>
    </aside>

    <!-- CONTROLADOR ALPINE PARA BARRA MÓVIL Y MENÚ FLOTANTE EXTENDIDO -->
    <div x-data="{ mobileMenu: false }" class="md:hidden">

        <!-- Barra de Navegación Inferior Ergonómica -->
        <nav
            class="fixed bottom-0 left-0 right-0 h-16 bg-surface-container-lowest border-t border-outline-variant flex items-center justify-around px-2 z-40 shadow-lg">

            <a href="{{ route('dashboard') }}"
                class="flex flex-col items-center gap-0.5 {{ request()->routeIs('dashboard') ? 'text-secondary font-bold' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined"
                    style="{{ request()->routeIs('dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">dashboard</span>
                <span class="text-[9px] uppercase tracking-wider">Home</span>
            </a>

            <a href="{{ route('sales') }}"
                class="flex flex-col items-center gap-0.5 {{ request()->routeIs('sales*') ? 'text-secondary font-bold' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined"
                    style="{{ request()->routeIs('sales*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">point_of_sale</span>
                <span class="text-[9px] uppercase tracking-wider">POS</span>
            </a>

            <a href="{{ route('products') }}"
                class="flex flex-col items-center gap-0.5 {{ request()->routeIs('products*') ? 'text-secondary font-bold' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined"
                    style="{{ request()->routeIs('products*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">inventory_2</span>
                <span class="text-[9px] uppercase tracking-wider">Inv</span>
            </a>

            <!-- Botón de Disparo del Cajón Flotante Opciones -->
            <button @click="mobileMenu = true" type="button"
                class="flex flex-col items-center gap-0.5 {{ request()->routeIs('customers*', 'suppliers*', 'accounts-receivable*', 'settings*') ? 'text-secondary font-bold' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined">more_horiz</span>
                <span class="text-[9px] uppercase tracking-wider">Menú</span>
            </button>
        </nav>

        <!-- Drawer Flotante (Bottom Sheet) para Módulos Adicionales -->
        <div x-show="mobileMenu" class="fixed inset-0 z-50 flex items-end justify-center" style="display: none;">
            <!-- Backdrop Oscuro -->
            <div x-show="mobileMenu" x-transition:fade @click="mobileMenu = false"
                class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

            <!-- Contenedor del menú -->
            <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="relative bg-surface-container-lowest w-full rounded-t-2xl border-t border-outline-variant shadow-2xl p-6 pb-10 space-y-4 z-10 max-h-[70vh] overflow-y-auto">

                <div class="w-12 h-1 bg-outline-variant/60 rounded-full mx-auto mb-2"></div>

                <div class="flex justify-between items-center border-b border-outline-variant/40 pb-3">
                    <h4 class="text-label-caps font-label-caps font-bold text-outline uppercase tracking-wider text-xs">
                        Módulos del Sistema</h4>
                    <button @click="mobileMenu = false"
                        class="p-1.5 text-on-surface-variant hover:bg-surface-container-high rounded-full"><span
                            class="material-symbols-outlined text-sm">close</span></button>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <a href="{{ route('customers') }}"
                        class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/60 {{ request()->routeIs('customers*') ? 'bg-secondary-fixed/30 text-secondary border-secondary font-semibold' : 'bg-surface hover:bg-surface-container-low text-primary' }}">
                        <span class="material-symbols-outlined">group</span>
                        <span class="text-xs font-bold uppercase tracking-wide">Clientes</span>
                    </a>

                    <a href="{{ route('suppliers') }}"
                        class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/60 {{ request()->routeIs('suppliers*') ? 'bg-secondary-fixed/30 text-secondary border-secondary font-semibold' : 'bg-surface hover:bg-surface-container-low text-primary' }}">
                        <span class="material-symbols-outlined">local_shipping</span>
                        <span class="text-xs font-bold uppercase tracking-wide">Proveedores</span>
                    </a>

                    <a href="{{ route('accounts-receivable') }}"
                        class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/60 {{ request()->routeIs('accounts-receivable*') ? 'bg-secondary-fixed/30 text-secondary border-secondary font-semibold' : 'bg-surface hover:bg-surface-container-low text-primary' }}">
                        <span class="material-symbols-outlined">payments</span>
                        <span class="text-xs font-bold uppercase tracking-wide">CxC</span>
                    </a>

                    <a href="{{ route('settings') }}"
                        class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/60 {{ request()->routeIs('settings*') ? 'bg-secondary-fixed/30 text-secondary border-secondary font-semibold' : 'bg-surface hover:bg-surface-container-low text-primary' }}">
                        <span class="material-symbols-outlined">settings</span>
                        <span class="text-xs font-bold uppercase tracking-wide">Config</span>
                    </a>
                    <a href="{{ route('categories') }}"
                        class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/60 {{ request()->routeIs('categories*') ? 'bg-secondary-fixed/30 text-secondary border-secondary font-semibold' : 'bg-surface hover:bg-surface-container-low text-primary' }}">
                        <span class="material-symbols-outlined">category</span>
                        <span class="text-xs font-bold uppercase tracking-wide">Categorías</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>