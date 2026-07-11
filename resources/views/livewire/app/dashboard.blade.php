<div class="flex-1 overflow-y-auto p-gutter custom-scrollbar">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-headline-lg font-headline-lg text-primary">Executive Overview</h2>
            <p class="text-body-md text-on-surface-variant">Real-time commercial performance metrics</p>
        </div>
        <div class="flex gap-2">
            <button
                class="px-4 py-2 bg-primary text-on-primary font-semibold rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>New Sale</span>
            </button>
            <button
                class="px-4 py-2 bg-surface-container-lowest border border-outline-variant text-on-surface font-semibold rounded-lg hover:bg-surface-container-low transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Export</span>
            </button>
        </div>
    </div>
    <!-- KPI Cards Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Monthly Sales -->
        <div
            class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-secondary/10 text-secondary rounded-lg">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <span class="text-label-caps text-green-600 bg-green-50 px-2 py-1 rounded-md">+12.5%</span>
            </div>
            <p class="text-label-caps text-on-surface-variant mb-1 uppercase">Monthly Sales</p>
            <h3 class="text-headline-lg font-bold text-primary">$15,400</h3>
        </div>
        <!-- Pending Receivables -->
        <div
            class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-surface-tint/10 text-surface-tint rounded-lg">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
            </div>
            <p class="text-label-caps text-on-surface-variant mb-1 uppercase">Pending Receivables</p>
            <h3 class="text-headline-lg font-bold text-primary">$3,200</h3>
        </div>
        <!-- Inventory Value -->
        <div
            class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-primary-container/10 text-on-primary-fixed-variant rounded-lg">
                    <span class="material-symbols-outlined">inventory</span>
                </div>
            </div>
            <p class="text-label-caps text-on-surface-variant mb-1 uppercase">Inventory Value</p>
            <h3 class="text-headline-lg font-bold text-primary">$45,000</h3>
        </div>
        <!-- Low Stock Alerts -->
        <div
            class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-error-container text-on-error-container rounded-lg">
                    <span class="material-symbols-outlined">priority_high</span>
                </div>
                <span
                    class="text-label-caps text-on-error-container bg-error-container px-2 py-1 rounded-md">Critical</span>
            </div>
            <p class="text-label-caps text-on-surface-variant mb-1 uppercase">Low Stock Alerts</p>
            <h3 class="text-headline-lg font-bold text-primary">5 Items</h3>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Ventas de la Semana (Chart Card) -->
        <div class="lg:col-span-2 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h4 class="font-headline-lg text-primary">Ventas de la Semana</h4>
                    <p class="text-body-sm text-on-surface-variant">Daily sales volume analysis</p>
                </div>
                <select class="bg-surface-container-low border-none rounded-lg text-label-caps px-3 py-1 outline-none">
                    <option>This Week</option>
                    <option>Last Week</option>
                </select>
            </div>
            <div class="h-64 flex items-end justify-between gap-4 px-2">
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="chart-bar w-full bg-secondary-fixed-dim rounded-t-lg hover:bg-secondary transition-colors"
                        style="height: 45%;"></div>
                    <span class="text-label-caps text-on-surface-variant">Lun</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="chart-bar w-full bg-secondary-fixed-dim rounded-t-lg hover:bg-secondary transition-colors"
                        style="height: 60%;"></div>
                    <span class="text-label-caps text-on-surface-variant">Mar</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="chart-bar w-full bg-secondary-fixed-dim rounded-t-lg hover:bg-secondary transition-colors"
                        style="height: 85%;"></div>
                    <span class="text-label-caps text-on-surface-variant">Mie</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="chart-bar w-full bg-secondary rounded-t-lg" style="height: 95%;"></div>
                    <span class="text-label-caps text-on-surface-variant font-bold text-secondary">Jue</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="chart-bar w-full bg-secondary-fixed-dim rounded-t-lg hover:bg-secondary transition-colors"
                        style="height: 55%;"></div>
                    <span class="text-label-caps text-on-surface-variant">Vie</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="chart-bar w-full bg-secondary-fixed-dim rounded-t-lg hover:bg-secondary transition-colors"
                        style="height: 40%;"></div>
                    <span class="text-label-caps text-on-surface-variant">Sab</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="chart-bar w-full bg-secondary-fixed-dim rounded-t-lg hover:bg-secondary transition-colors"
                        style="height: 30%;"></div>
                    <span class="text-label-caps text-on-surface-variant">Dom</span>
                </div>
            </div>
        </div>
        <!-- Recent Activity/Mini Bento -->
        <div class="flex flex-col gap-6">
            <div class="bg-surface-tint text-on-secondary p-6 rounded-xl relative overflow-hidden flex-1 min-h-[200px]">
                <h4 class="font-headline-lg mb-2 relative z-10">Stock Performance</h4>
                <p class="text-body-sm opacity-80 relative z-10">94% of your inventory is currently healthy.</p>
                <div class="mt-6 flex items-center gap-4 relative z-10">
                    <button
                        class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-label-caps transition-all">Optimize
                        Now</button>
                </div>
                <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
            </div>
            <div class="bg-secondary p-6 rounded-xl text-on-secondary shadow-lg">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined">trending_up</span>
                    </div>
                    <div>
                        <p class="text-label-caps opacity-80 uppercase">Top Client This Month</p>
                        <p class="text-headline-lg font-bold">Industrial Solutions Ltd</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Últimas 5 Ventas (Table Card) -->
    <div class="mt-8 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div
            class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
            <h4 class="font-headline-lg text-primary">Últimas 5 Ventas</h4>
            <a class="text-secondary text-label-caps font-bold hover:underline" href="#">View All Records</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-6 py-4 text-label-caps text-on-surface-variant uppercase">ID</th>
                        <th class="px-6 py-4 text-label-caps text-on-surface-variant uppercase">Date</th>
                        <th class="px-6 py-4 text-label-caps text-on-surface-variant uppercase">Customer</th>
                        <th class="px-6 py-4 text-label-caps text-on-surface-variant uppercase">Total</th>
                        <th class="px-6 py-4 text-label-caps text-on-surface-variant uppercase">Status</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4 font-data-mono text-primary">#NX-9021</td>
                        <td class="px-6 py-4 text-body-sm">Oct 24, 2023</td>
                        <td class="px-6 py-4 text-body-sm font-semibold">Global Tech Corp</td>
                        <td class="px-6 py-4 text-body-sm font-bold">$2,450.00</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Success
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="p-1 hover:bg-surface-container-high rounded transition-all">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4 font-data-mono text-primary">#NX-9020</td>
                        <td class="px-6 py-4 text-body-sm">Oct 24, 2023</td>
                        <td class="px-6 py-4 text-body-sm font-semibold">Sarah Jenkins</td>
                        <td class="px-6 py-4 text-body-sm font-bold">$125.50</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="p-1 hover:bg-surface-container-high rounded transition-all">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4 font-data-mono text-primary">#NX-9019</td>
                        <td class="px-6 py-4 text-body-sm">Oct 23, 2023</td>
                        <td class="px-6 py-4 text-body-sm font-semibold">Vertex Logistics</td>
                        <td class="px-6 py-4 text-body-sm font-bold">$4,100.00</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Success
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="p-1 hover:bg-surface-container-high rounded transition-all">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4 font-data-mono text-primary">#NX-9018</td>
                        <td class="px-6 py-4 text-body-sm">Oct 23, 2023</td>
                        <td class="px-6 py-4 text-body-sm font-semibold">Robert Chen</td>
                        <td class="px-6 py-4 text-body-sm font-bold">$890.00</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Success
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="p-1 hover:bg-surface-container-high rounded transition-all">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4 font-data-mono text-primary">#NX-9017</td>
                        <td class="px-6 py-4 text-body-sm">Oct 22, 2023</td>
                        <td class="px-6 py-4 text-body-sm font-semibold">Aero Dynamic Parts</td>
                        <td class="px-6 py-4 text-body-sm font-bold">$12,400.00</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="p-1 hover:bg-surface-container-high rounded transition-all">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // Micro-interactions and effects
        document.addEventListener('DOMContentLoaded', () => {
            // Animate bar chart heights on load
            const bars = document.querySelectorAll('.chart-bar');
            bars.forEach(bar => {
                const targetHeight = bar.style.height;
                bar.style.height = '0%';
                setTimeout(() => {
                    bar.style.height = targetHeight;
                }, 300);
            });

            // Tab switching visual effect (Client-side simulation)
            const navItems = document.querySelectorAll('nav a');
            navItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    if (item.classList.contains('bg-secondary-fixed/30')) return; // already active

                    // This is just a visual demo of the interaction
                    navItems.forEach(i => {
                        i.className = "flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-outline hover:bg-surface-container-high transition-colors transition-all";
                    });

                    item.className = "flex items-center gap-3 px-4 py-3 border-l-4 border-secondary bg-secondary-fixed/30 text-secondary dark:text-secondary-fixed font-semibold opacity-90 transition-opacity duration-150";
                });
            });
        });
    </script>
</div>