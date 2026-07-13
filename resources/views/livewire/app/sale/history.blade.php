<div class="p-margin-mobile md:p-gutter space-y-6">
    
    <!-- Encabezado del Historial -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-headline-lg font-headline-lg text-primary">Historial de Ventas</h2>
            <p class="text-body-md text-on-surface-variant">Consulte, audite y verifique los detalles de las transacciones facturadas en el POS.</p>
        </div>
    </div>

    <!-- Barra de Filtros Operativa Avanzada -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-surface-container-low p-4 rounded-xl border border-outline-variant/60">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-outline uppercase">Buscar Factura / Cliente</label>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full px-3 py-1.5 bg-surface border border-outline-variant rounded-lg text-xs" placeholder="Ej: #00234 o Nombre..." />
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-outline uppercase">Desde</label>
            <input type="date" wire:model.live="dateFrom" class="w-full px-3 py-1.5 bg-surface border border-outline-variant rounded-lg text-xs" />
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-outline uppercase">Hasta</label>
            <input type="date" wire:model.live="dateTo" class="w-full px-3 py-1.5 bg-surface border border-outline-variant rounded-lg text-xs" />
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-outline uppercase">Condición de Pago</label>
            <select wire:model.live="paymentMethodFilter" class="w-full px-3 py-1.5 bg-surface border border-outline-variant rounded-lg text-xs">
                <option value="">Todos los métodos</option>
                <option value="cash">Efectivo</option>
                <option value="card">Punto / Pago Móvil</option>
                <option value="credit">A Crédito</option>
            </select>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-label-caps text-on-surface-variant text-xs">
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant">N° Factura</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant">Cliente / RIF</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-center">Fecha y Hora</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-center">Método</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-right">Total ($)</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-right">Total (Bs.)</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-body-sm divide-y divide-outline-variant">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                            <td class="px-6 py-4 font-bold font-data-mono text-primary">{{ $sale->invoice_number }}</td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-primary">{{ $sale->customer?->name ?? 'Consumidor Final' }}</p>
                                <p class="text-xs text-outline font-data-mono">{{ $sale->customer?->rif_ci ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center font-data-mono text-xs">{{ $sale->created_at->format('Y-m-d h:i A') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($sale->payment_method === 'credit')
                                    <span class="px-2.5 py-0.5 rounded-full bg-error-container text-on-error-container text-[10px] font-bold uppercase tracking-wider">A Crédito</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-secondary-fixed text-secondary text-[10px] font-bold uppercase tracking-wider">
                                        {{ $sale->payment_method === 'cash' ? 'Efectivo' : 'Punto/PM' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold font-data-mono text-primary">${{ number_format($sale->total, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold font-data-mono text-secondary">Bs. {{ number_format($sale->total * $exchangeRate, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" wire:click="openDetailModal('{{ $sale->id }}')" class="text-secondary hover:text-primary font-bold text-xs inline-flex items-center gap-1 hover:underline">
                                    Ver Detalle <span class="material-symbols-outlined text-sm">visibility</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">No se encontraron registros de ventas bajo los filtros aplicados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-surface-container-low border-t border-outline-variant">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Modal de Detalles de Venta (FIXED: Perfectamente Centrado) -->
    @if($detailModal && $selectedSale)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" wire:click="closeDetailModal"></div>
            
            <div class="relative bg-surface-container-lowest w-full max-w-lg rounded-2xl border border-outline-variant shadow-2xl flex flex-col transform animate-in zoom-in duration-150 z-10 max-h-[85vh]">
                <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                    <div>
                        <h3 class="font-bold text-primary text-sm uppercase tracking-wider">Comprobante de Venta</h3>
                        <p class="text-xs font-data-mono text-secondary">Factura: {{ $selectedSale->invoice_number }}</p>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="p-1 hover:bg-surface-container-high rounded-full"><span class="material-symbols-outlined">close</span></button>
                </div>
                
                <!-- Cuerpo del Recibo / Auditoría -->
                <div class="p-6 overflow-y-auto space-y-4 flex-1 custom-scrollbar text-xs">
                    <div class="grid grid-cols-2 gap-4 border-b border-outline-variant/40 pb-3">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-outline">Cliente</p>
                            <p class="font-bold text-primary text-body-sm">{{ $selectedSale->customer?->name ?? 'Consumidor Final' }}</p>
                            <p class="font-data-mono text-outline-variant">{{ $selectedSale->customer?->rif_ci }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase font-bold text-outline">Fecha de Emisión</p>
                            <p class="font-bold text-primary">{{ $selectedSale->created_at->format('Y-m-d h:i A') }}</p>
                        </div>
                    </div>

                    <!-- Listado de Artículos Despachados -->
                    <div class="space-y-2">
                        <p class="text-[10px] uppercase font-bold text-outline tracking-wider">Artículos Facturados</p>
                        <div class="divide-y divide-outline-variant/30 border border-outline-variant rounded-xl overflow-hidden bg-surface">
                            @foreach($selectedSale->items as $item)
                                <div class="p-3 flex justify-between items-center gap-4">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-primary truncate">{{ $item->product?->name ?? 'Producto Eliminado' }}</p>
                                        <p class="text-outline font-data-mono">{{ $item->quantity }}x ${{ number_format($item->unit_price, 2) }}</p>
                                    </div>
                                    <span class="font-bold font-data-mono text-primary">${{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Resumen Financiero Totalizado -->
                    <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant space-y-2 text-body-sm">
                        <div class="flex justify-between font-medium text-on-surface-variant">
                            <span>Método Utilizado:</span>
                            <span class="font-bold uppercase text-primary">{{ $selectedSale->payment_method }}</span>
                        </div>
                        <div class="flex justify-between border-t border-outline-variant/40 pt-2 text-base font-bold text-primary">
                            <span>Total General ($):</span>
                            <span class="font-data-mono">${{ number_format($selectedSale->total, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs font-bold text-secondary">
                            <span>Total General (Bs.):</span>
                            <span class="font-data-mono">Bs. {{ number_format($selectedSale->total * $exchangeRate, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-surface-container-low border-t border-outline-variant flex justify-end gap-2">
                    <button type="button" onclick="window.print()" class="px-4 py-1.5 border border-outline-variant bg-white text-primary font-bold text-xs rounded-xl hover:bg-surface-container">
                        Imprimir Recibo
                    </button>
                    <button type="button" wire:click="closeDetailModal" class="px-5 py-1.5 bg-primary text-on-primary font-bold text-xs rounded-xl hover:opacity-95">
                        Cerrar Ventana
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>