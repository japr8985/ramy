<div class="flex-1 overflow-y-auto p-margin-mobile md:p-gutter custom-scrollbar pb-24 md:pb-6">
    
    <!-- Encabezado Principal y Selector de Rangos Dinámicos -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-outline-variant/40 pb-6">
        <div>
            <h2 class="text-headline-lg font-headline-lg text-primary tracking-tight">Executive Overview</h2>
            <p class="text-body-md text-on-surface-variant">Inteligencia de negocio y auditoría de caja parametrizada.</p>
        </div>
        
        <!-- Filtro de Fechas Compacto del Dashboard -->
        <div class="flex flex-wrap items-center gap-3 bg-surface-container-low p-3 rounded-xl border border-outline-variant/60 w-full lg:w-auto justify-end">
            <div class="flex items-center gap-2 text-xs font-semibold text-outline">
                <span class="material-symbols-outlined text-sm">date_range</span>
                <span>Período:</span>
            </div>
            <input type="date" wire:model.live="dateFrom" class="px-3 py-1.5 bg-surface border border-outline-variant rounded-lg text-xs font-data-mono text-primary focus:ring-1 focus:ring-secondary" />
            <span class="text-xs text-outline font-bold">al</span>
            <input type="date" wire:model.live="dateTo" class="px-3 py-1.5 bg-surface border border-outline-variant rounded-lg text-xs font-data-mono text-primary focus:ring-1 focus:ring-secondary" />
        </div>
    </div>

    <!-- KPI Cards Bento Grid Dinámico -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Ventas del Rango Seleccionado -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2 bg-secondary/10 text-secondary rounded-lg">
                    <span class="material-symbols-outlined text-md">payments</span>
                </div>
                <span class="text-[10px] font-bold text-secondary bg-secondary-fixed-dim px-2 py-0.5 rounded uppercase tracking-wider">Facturado</span>
            </div>
            <p class="text-[10px] font-label-caps text-on-surface-variant mb-0.5 uppercase tracking-wider">Ventas en Período</p>
            <h3 class="text-xl md:text-2xl font-bold font-data-mono text-primary">${{ number_format($kpis['filtered_sales'], 2, ',', '.') }}</h3>
            <p class="text-[11px] font-medium text-outline mt-1">Ref: Bs. {{ number_format($kpis['filtered_sales'] * $exchangeRate, 2, ',', '.') }}</p>
        </div>

        <!-- Cuentas por Cobrar (Saldos vivos totales) -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2 bg-primary/10 text-primary rounded-lg">
                    <span class="material-symbols-outlined text-md">pending_actions</span>
                </div>
                <a href="{{ route('accounts-receivable') }}" class="text-[10px] font-bold text-secondary hover:underline">Gestionar</a>
            </div>
            <p class="text-[10px] font-label-caps text-on-surface-variant mb-0.5 uppercase tracking-wider">Créditos Pendientes</p>
            <h3 class="text-xl md:text-2xl font-bold font-data-mono text-primary">${{ number_format($kpis['pending_receivables'], 2, ',', '.') }}</h3>
            <p class="text-[11px] font-medium text-outline mt-1">Ref: Bs. {{ number_format($kpis['pending_receivables'] * $exchangeRate, 2, ',', '.') }}</p>
        </div>

        <!-- Valor del Inventario -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2 bg-surface-tint/10 text-surface-tint rounded-lg">
                    <span class="material-symbols-outlined text-md">inventory</span>
                </div>
            </div>
            <p class="text-[10px] font-label-caps text-on-surface-variant mb-0.5 uppercase tracking-wider">Costo de Inventario</p>
            <h3 class="text-xl md:text-2xl font-bold font-data-mono text-primary">${{ number_format($kpis['inventory_value'], 2, ',', '.') }}</h3>
            <p class="text-[11px] font-medium text-outline mt-1">Capital amarrado en stock actual.</p>
        </div>

        <!-- Alertas de Stock Crítico -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs {{ $kpis['low_stock_count'] > 0 ? 'border-error/40 bg-error-container/5' : '' }}">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2 {{ $kpis['low_stock_count'] > 0 ? 'bg-error/10 text-error' : 'bg-success/10 text-success' }} rounded-lg">
                    <span class="material-symbols-outlined text-md">priority_high</span>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $kpis['low_stock_count'] > 0 ? 'bg-error-container text-on-error-container' : 'bg-success/10 text-success' }}">
                    {{ $kpis['low_stock_count'] > 0 ? 'Crítico' : 'Estable' }}
                </span>
            </div>
            <p class="text-[10px] font-label-caps text-on-surface-variant mb-0.5 uppercase tracking-wider">Alertas de Stock</p>
            <h3 class="text-xl md:text-2xl font-bold font-data-mono {{ $kpis['low_stock_count'] > 0 ? 'text-error' : 'text-primary' }}">{{ $kpis['low_stock_count'] }} SKUs</h3>
            <p class="text-[11px] font-medium text-outline mt-1">Artículos por debajo del mínimo.</p>
        </div>
    </div>

    <!-- Gráfica Escalamiento y Tarjetas Informativas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-xs flex flex-col justify-between">
            <div class="mb-6">
                <h4 class="font-bold text-primary text-xs uppercase tracking-wider">Análisis Temporal de Facturación</h4>
                <p class="text-xs text-on-surface-variant mt-0.5">Volumen consolidado en dólares ($) adaptado según la amplitud de fechas del filtro.</p>
            </div>
            
            <div class="h-48 flex items-end justify-between gap-2 md:gap-4 px-2 overflow-x-auto min-w-0">
                @foreach($chartBars as $bar)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group relative min-w-[30px]">
                        <span class="absolute -top-8 bg-primary text-on-primary text-[10px] font-bold px-1.5 py-0.5 rounded shadow opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none font-data-mono whitespace-nowrap">
                            ${{ number_format($bar['total'], 0, ',', '.') }}
                        </span>
                        
                        <div class="chart-bar w-full rounded-t-md transition-all duration-500 {{ $bar['is_current'] ? 'bg-primary' : 'bg-secondary-fixed-dim hover:bg-secondary' }}"
                            style="height: {{ $bar['height_percentage'] }}%;"></div>
                        
                        <span class="text-[9px] font-bold text-center tracking-tighter truncate w-full {{ $bar['is_current'] ? 'text-primary font-black' : 'text-outline' }}" title="{{ $bar['label'] }}">
                            {{ $bar['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bloque Lateral Métricas Rápidas (FIXED: Ahora calcula la utilidad real) -->
        <div class="flex flex-col gap-4 justify-between">
            <!-- Nueva tarjeta de Margen de Utilidad -->
            <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex flex-col justify-between min-h-[140px]">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-label-caps text-on-surface-variant mb-0.5 uppercase tracking-wider">Ganancia Neta (Período)</p>
                        <h3 class="text-xl font-bold font-data-mono text-success">${{ number_format($profit['net_amount'], 2, ',', '.') }}</h3>
                        <p class="text-[11px] font-medium text-outline mt-0.5">Ref: Bs. {{ number_format($profit['net_amount'] * $exchangeRate, 2, ',', '.') }}</p>
                    </div>
                    <div class="p-2 bg-success/10 text-success rounded-lg shrink-0">
                        <span class="material-symbols-outlined text-md">trending_up</span>
                    </div>
                </div>
                
                <div class="pt-3 border-t border-outline-variant/40 flex items-center justify-between text-[11px]">
                    <span class="text-on-surface-variant font-medium">Margen sobre costo:</span>
                    <span class="font-bold font-data-mono text-success bg-success/10 px-2 py-0.5 rounded-md">
                        {{ $profit['margin_percentage'] }}%
                    </span>
                </div>
            </div>
            
            <!-- Tarjeta del Top Cliente (Conservada y acoplada abajo) -->
            <div class="bg-secondary text-on-secondary p-5 rounded-xl flex items-center gap-4 shadow-sm shrink-0">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-md">star</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[9px] font-bold uppercase tracking-wider opacity-75">Top Comprador en Período</p>
                    <p class="text-sm font-bold truncate">{{ $topCustomer }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN INFERIOR DUAL: RECIENTES DEL PERIODO VS STOCK CRÍTICO -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-xs overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                <h4 class="font-bold text-primary text-xs uppercase tracking-wider">Ventas en el Período (Max 5)</h4>
                <a class="text-secondary text-[11px] font-bold hover:underline uppercase tracking-wide" href="{{ route('sales.history') }}">Ver Todo</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse min-w-[500px]">
                    <thead>
                        <tr class="bg-surface-container-low/30 text-[10px] text-on-surface-variant font-label-caps border-b border-outline-variant">
                            <th class="px-5 py-3 font-semibold">Factura</th>
                            <th class="px-5 py-3 font-semibold">Cliente</th>
                            <th class="px-5 py-3 font-semibold text-right">Total ($)</th>
                            <th class="px-5 py-3 font-semibold text-center">Método</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/60 text-xs">
                        @foreach($recentSales as $sale)
                            <tr class="hover:bg-surface-container-low/30 transition-colors">
                                <td class="px-5 py-3.5 font-bold font-data-mono text-primary">{{ $sale->invoice_number }}</td>
                                <td class="px-5 py-3.5 font-semibold text-primary truncate max-w-[150px]">{{ $sale->customer?->name ?? 'Consumidor Final' }}</td>
                                <td class="px-5 py-3.5 text-right font-bold font-data-mono">${{ number_format($sale->total, 2, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $sale->payment_method === 'credit' ? 'bg-error-container text-on-error-container' : 'bg-surface-container-highest text-primary' }}">
                                        {{ $sale->payment_method === 'credit' ? 'Crédito' : ($sale->payment_method === 'cash' ? 'Efectivo' : 'Punto/PM') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Panel Bento de Stock Crítico -->
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-xs overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                <h4 class="font-bold text-error text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">warning</span> 
                    Reposición / Stock Crítico
                </h4>
                <a class="text-secondary text-[11px] font-bold hover:underline uppercase tracking-wide" href="{{ route('products') }}">Ver Inv</a>
            </div>
            
            <div class="divide-y divide-outline-variant/60 overflow-y-auto flex-1 text-xs">
                @forelse($criticalProducts as $prod)
                    <div class="p-4 flex items-center justify-between gap-3 hover:bg-error-container/5 transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-primary truncate">{{ $prod->name }}</p>
                            <p class="text-[10px] text-outline font-data-mono">Mínimo requerido: {{ $prod->min_stock }} un.</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-1 rounded-lg font-data-mono font-bold text-xs bg-error-container text-on-error-container">
                                {{ $prod->quantity }} un.
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-outline space-y-2 py-12">
                        <span class="material-symbols-outlined text-3xl text-success opacity-60">check_circle</span>
                        <p class="text-xs font-semibold">¡Inventario Saludable!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>