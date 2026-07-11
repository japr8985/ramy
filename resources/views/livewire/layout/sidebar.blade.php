<div>
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
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('dashboard')
    ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold'
    : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-caps text-label-caps">Dashboard</span>
            </a>

            <!-- Inventory/Products Link -->
            <a href="{{ route('products') }}" class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('products*')
    ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold'
    : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">inventory_2</span>
                <span class="font-label-caps text-label-caps">Inventario</span>
            </a>

            <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-outline hover:bg-surface-container-high transition-colors"
                href="{{ route('sales') }}">
                <span class="material-symbols-outlined" data-icon="point_of_sale">point_of_sale</span>
                <span class="text-body-md font-body-md">Sales (POS)</span>
            </a>

            <!-- Customers Link -->
            <a href="{{ route('customers') }}" class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('customers*')
    ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold'
    : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">group</span>
                <span class="font-label-caps text-label-caps">Clientes</span>
            </a>

            <!-- suppliers Link -->
            <a href="{{ route('suppliers') }}" class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('suppliers*')
    ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold'
    : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">group</span>
                <span class="font-label-caps text-label-caps">Proveedores</span>
            </a>
            <!-- Cuentas por cobrar Link -->
            <a href="{{ route('accounts-receivable') }}" class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('accounts-receivable*')
    ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold'
    : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">payments</span>
                <span class="font-label-caps text-label-caps">Cuentas por cobrar</span>
            </a>
            <!-- Usuarios Link -->
            {{-- <a href="{{ route('accounts-receivable') }}" class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('accounts-receivable*')
    ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold'
    : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">person</span>
                <span class="font-label-caps text-label-caps">Usuarios</span>
            </a> --}}
            <!-- Configuracion Link -->
            <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-3 transition-colors {{ request()->routeIs('settings*')
    ? 'border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold'
    : 'text-on-surface-variant dark:text-outline hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-label-caps text-label-caps">Config</span>
            </a>




        </nav>

        <div class="p-6 border-t border-outline-variant flex items-center gap-3">
            <img class="w-10 h-10 rounded-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAuPTDCyLn9GjwQRXhnwarLhfNbAu7or-XxZBFHcASqzbwT_X4jhKbWL1WiQlWexPl5fmHWG_uKKlSsAEiRWYbaiVDHCYWBQb5e8o7aF7qDFljWlWxT4x0SSa9_I_YlAOvDJjVo9BIt1mtkYHtYLbxY7Q4qKBNCJydtB9Bw8xA6gK84v_CI4Cq9J-9px5HiZUB6Vn_Ebj-9R712qN7JOmfFjUYPiAeHO3vrK2kc6xyx6H9iazkQGchJ0Q"
                alt="Logistics Manager Avatar" />
            <div>
                <p class="font-label-caps text-label-caps font-bold">Admin User</p>
                <p class="text-[10px] uppercase text-on-surface-variant">Systems Overseer</p>
            </div>
        </div>
    </aside>
    <nav
        class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-surface-container-lowest border-t border-outline-variant flex items-center justify-around px-4 z-50">
        <button class="flex flex-col items-center gap-1 text-on-surface-variant">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-[10px] font-bold uppercase">Home</span>
        </button>
        <button class="flex flex-col items-center gap-1 text-on-surface-variant">
            <span class="material-symbols-outlined">inventory_2</span>
            <span class="text-[10px] font-bold uppercase">Inv</span>
        </button>
        <button class="flex flex-col items-center gap-1 text-secondary">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">local_shipping</span>
            <span class="text-[10px] font-bold uppercase">Supp</span>
        </button>
        <button class="flex flex-col items-center gap-1 text-on-surface-variant">
            <span class="material-symbols-outlined">settings</span>
            <span class="text-[10px] font-bold uppercase">Set</span>
        </button>
    </nav>
    <script>
        // Toggle mobile menu logic (Placeholder)
        const mobileMenuBtn = document.querySelector('.md\\:hidden.p-2');
        mobileMenuBtn?.addEventListener('click', () => {
            console.log('Mobile menu toggled');
            // Logic to show/hide a mobile drawer could go here
        });
    </script>
</div>