<div class="p-margin-mobile md:p-gutter space-y-6">

    <!-- Encabezado de Sección -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-headline-lg font-headline-lg text-primary">Cuentas por Cobrar (Créditos)</h2>
            <p class="text-body-md text-on-surface-variant">Gestione los saldos pendientes de sus clientes y el registro de cobranzas de manera centralizada.</p>
        </div>
        <div class="relative w-full md:max-w-xs">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm">search</span>
            <input type="text" wire:model.live.debounce.300ms="search"
                class="w-full pl-9 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm focus:ring-2 focus:ring-secondary"
                placeholder="Buscar por cliente o factura..." />
        </div>
    </div>

    <!-- Bento Stats Dinámicos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs">
            <p class="text-label-caps font-label-caps text-on-surface-variant mb-1">Total por Cobrar ($)</p>
            <h3 class="text-headline-lg font-bold text-primary">${{ number_format($stats['total_receivables'], 2) }}</h3>
            <p class="text-xs text-outline mt-1">Ref: Bs. {{ number_format($stats['total_receivables'] * $exchangeRate, 2, ',', '.') }}</p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs">
            <p class="text-label-caps font-label-caps text-on-surface-variant mb-1">Monto Vencido</p>
            <h3 class="text-headline-lg font-bold text-error">${{ number_format($stats['overdue_amount'], 2) }}</h3>
            <p class="text-xs text-error font-medium mt-1">Requiere gestión de cobro inmediata</p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs">
            <p class="text-label-caps font-label-caps text-on-surface-variant mb-1">Recaudado este Mes</p>
            <h3 class="text-headline-lg font-bold text-success">${{ number_format($stats['collected_mtd'], 2) }}</h3>
            <p class="text-xs text-outline mt-1">Abonos procesados vía POS / Banco</p>
        </div>
    </div>

    <!-- Tabla Principal de Facturas de Crédito Activas -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-xs overflow-hidden">
        <div class="p-4 border-b border-outline-variant bg-surface-container-low/50">
            <h4 class="font-bold text-on-surface text-sm uppercase">Registro de Créditos Activos</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-label-caps text-on-surface-variant select-none text-xs">
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant">Cliente / Factura</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-center">Fecha Emisión</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-right">Total ($)</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-right">Monto Abonado</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-right">Saldo Deudor ($)</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-right">Saldo Deudor (Bs.)</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-center">Vencimiento</th>
                        <th class="px-6 py-4 font-semibold border-b border-outline-variant text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-body-sm divide-y divide-outline-variant">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-surface-container flex items-center justify-center text-outline">
                                        <span class="material-symbols-outlined text-lg">person</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-primary">{{ $invoice->customer?->name ?? 'Cliente General' }}</p>
                                        <p class="text-xs text-on-surface-variant font-data-mono">{{ $invoice->sale?->invoice_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-data-mono text-xs">{{ \Carbon\Carbon::parse($invoice->sale_date)->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-right font-data-mono">${{ number_format($invoice->total, 2) }}</td>
                            <td class="px-6 py-4 text-right font-data-mono text-success">${{ number_format($invoice->paid_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right font-bold font-data-mono text-primary">${{ number_format($invoice->balance, 2) }}</td>
                            <td class="px-6 py-4 text-right font-bold font-data-mono text-secondary">Bs. {{ number_format($invoice->balance_ves, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if(\Carbon\Carbon::parse($invoice->due_date)->isPast())
                                    <span class="inline-flex items-center px-2 py-0.5 bg-error-container text-on-error-container rounded text-xs font-bold gap-1 mx-auto w-max">
                                        <span class="material-symbols-outlined text-xs">event_busy</span> Vencido
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 bg-surface-container-highest text-primary rounded text-xs font-bold gap-1 mx-auto w-max">
                                        <span class="material-symbols-outlined text-xs">schedule</span> En Plazo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button wire:click="openPaymentModal('{{ $invoice->id }}')"
                                        class="text-secondary hover:text-primary font-bold text-xs inline-flex items-center gap-1 hover:underline">
                                        Abonar <span class="material-symbols-outlined text-sm">add_circle</span>
                                    </button>
                                    <button wire:click="openHistoryModal('{{ $invoice->id }}')" 
                                        class="text-outline hover:text-primary font-medium text-xs inline-flex items-center gap-0.5 hover:underline" title="Ver historial de pagos">
                                        Historial <span class="material-symbols-outlined text-sm">history</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-on-surface-variant text-body-sm">No hay cuentas de crédito pendientes por cobrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-surface-container-low border-t border-outline-variant">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Modal de Registro de Abonos -->
    <div class="{{ $modal ? '' : 'hidden' }} fixed inset-0 z-50 flex items-end md:items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" wire:click="closePaymentModal"></div>

        <div class="relative bg-surface-container-lowest w-full max-w-md rounded-t-2xl md:rounded-2xl shadow-2xl overflow-hidden border border-outline-variant flex flex-col transform animate-in slide-in-from-bottom duration-200 z-10">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <h3 class="text-headline-lg font-bold text-primary">Registrar Abono</h3>
                <button class="text-on-surface-variant hover:text-error transition-colors" type="button" wire:click="closePaymentModal">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- FORMULARIO ELECTRÓNICO REDISEÑADO CON ALPINEJS -->
            <form wire:submit.prevent="postPayment" class="p-6 space-y-4"
    x-data="{
        rate: @js($exchangeRate),
        maxUsd: @entangle('current_invoice_balance'), // Enlazado de forma reactiva en tiempo real
        currency: @entangle('input_currency').live,
        usd: @entangle('amount_usd'),
        ves: @entangle('amount_ves'),
        
        init() {
            // Cada vez que Livewire asigne una nueva factura al modal, Alpine actualiza los topes
            $watch('maxUsd', value => {
                let parsedMax = parseFloat(value) || 0;
                if (parsedMax > 0) {
                    this.usd = parsedMax;
                    this.ves = (parsedMax * this.rate).toFixed(2);
                }
            });

            // Escuchar cambios en el switch de moneda
            $watch('currency', value => {
                this.usd = 0;
                this.ves = (0).toFixed(2);
            });
        },
        calculateFromUsd() {
            let parsed = parseFloat(this.usd) || 0;
            let currentMax = parseFloat(this.maxUsd) || 0;
            if (parsed > currentMax) { parsed = currentMax; this.usd = currentMax; }
            if (parsed < 0) { parsed = 0; this.usd = 0; }
            this.ves = (parsed * this.rate).toFixed(2);
        },
        calculateFromVes() {
            let parsed = parseFloat(this.ves) || 0;
            let currentMax = parseFloat(this.maxUsd) || 0;
            let maxVes = currentMax * this.rate;
            if (parsed > maxVes) { parsed = maxVes; this.ves = maxVes.toFixed(2); }
            if (parsed < 0) { parsed = 0; this.ves = (0).toFixed(2); }
            this.usd = this.rate > 0 ? parseFloat((parsed / this.rate).toFixed(4)) : 0;
        }
    }">
                
                <div>
                    <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-0.5">Cliente</p>
                    <p class="text-headline-lg font-bold text-secondary">{{ $customer_name }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-outline uppercase mb-1">Deuda Pendiente</label>
                        <div class="text-xs font-bold font-data-mono bg-surface-container p-2 rounded border border-outline-variant text-primary">
                            ${{ number_format($current_invoice_balance, 2) }}<br>
                        </div>
                        <span class="text-[10px] font-normal text-secondary">Bs. {{ number_format($current_invoice_balance * $exchangeRate, 2, ',', '.') }}</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-outline uppercase mb-1">Fecha de Recepción</label>
                        <input type="date" wire:model="payment_date" class="w-full bg-surface border-outline-variant rounded-lg text-body-sm focus:ring-secondary py-1.5" />
                    </div>
                </div>

                <!-- SWITCH / CONMUTADOR DE MONEDA DE ENTRADA -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-outline uppercase">Moneda de Recepción en Caja</label>
                    <div class="grid grid-cols-2 bg-surface border border-outline-variant p-1 rounded-xl">
                        <button type="button" @click="currency = 'USD'" :class="currency === 'USD' ? 'bg-primary text-on-primary shadow-xs' : 'text-on-surface-variant hover:text-primary'" class="py-1.5 rounded-lg text-xs font-bold transition-all">
                            DÓLARES ($)
                        </button>
                        <button type="button" @click="currency = 'VES'" :class="currency === 'VES' ? 'bg-secondary text-on-secondary shadow-xs' : 'text-on-surface-variant hover:text-primary'" class="py-1.5 rounded-lg text-xs font-bold transition-all">
                            BOLÍVARES (Bs.)
                        </button>
                    </div>
                </div>

                <!-- INPUTS EN PARALELO CONTROLADOS POR ESTADOS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Input en Dólares -->
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-outline uppercase">Monto en Dólares ($)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-on-surface-variant font-bold text-xs" :class="currency !== 'USD' && 'opacity-40'">$</span>
                            <input type="number" step="0.0001" x-model="usd" @input="calculateFromUsd()"
                                :readonly="currency !== 'USD'"
                                :disabled="currency !== 'USD'"
                                :class="currency !== 'USD' ? 'bg-surface-container opacity-60 text-outline-variant cursor-not-allowed select-none' : 'bg-surface-container-lowest focus:ring-2 focus:ring-primary font-bold text-primary'"
                                class="w-full pl-7 pr-3 py-2.5 border border-outline-variant rounded-lg text-body-md font-data-mono" />
                        </div>
                    </div>

                    <!-- Input en Bolívares -->
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-outline uppercase">Monto en Bolívares (Bs.)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-on-surface-variant font-bold text-xs" :class="currency !== 'VES' && 'opacity-40'">Bs.</span>
                            <input type="number" step="0.01" x-model="ves" @input="calculateFromVes()"
                                :readonly="currency !== 'VES'"
                                :disabled="currency !== 'VES'"
                                :class="currency !== 'VES' ? 'bg-surface-container opacity-60 text-outline-variant cursor-not-allowed select-none' : 'bg-surface-container-lowest focus:ring-2 focus:ring-secondary font-bold text-secondary'"
                                class="w-full pl-10 pr-3 py-2.5 border border-outline-variant rounded-lg text-body-md font-data-mono" />
                        </div>
                    </div>
                </div>

                <!-- Mensaje Informativo de Tasas de Cambio -->
                <div class="p-3 bg-surface border border-outline-variant/60 rounded-xl flex items-center justify-between text-[11px] text-on-surface-variant font-medium">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-xs text-outline">info</span> Tasa BCV Oficial</span>
                    <span class="font-data-mono font-bold text-primary">Bs. {{ number_format($exchangeRate, 2, ',', '.') }}</span>
                </div>
                
                @error('amount_usd') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-outline uppercase">Método</label>
                        <select wire:model="payment_method" class="w-full bg-surface border-outline-variant rounded-lg text-body-sm py-2">
                            <option value="Bank Transfer">Pago Móvil / Transf.</option>
                            <option value="Cash">Efectivo</option>
                            <option value="Credit Card">Punto de Venta</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-outline uppercase">Referencia / Notas</label>
                        <input type="text" wire:model="notes" class="w-full bg-surface border-outline-variant rounded-lg text-body-sm py-2" placeholder="Opcional" />
                    </div>
                </div>

                <div class="pt-4 border-t border-outline-variant/40 flex justify-end gap-3">
                    <button type="button" class="px-5 py-2 border border-outline-variant bg-white text-on-surface font-semibold rounded-lg hover:bg-surface-container transition-all text-xs" wire:click="closePaymentModal">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-primary text-on-primary font-bold rounded-lg hover:opacity-90 transition-all shadow-md text-xs">Registrar Cobro</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Historial de Pagos Cronológico de la Cuenta -->
    <div class="{{ $historyModal ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" wire:click="closeHistoryModal"></div>

        <div class="relative bg-surface-container-lowest w-full max-w-lg rounded-2xl border border-outline-variant shadow-2xl flex flex-col transform animate-in zoom-in duration-150 z-10 max-h-[85vh]">
            <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <div>
                    <h3 class="font-bold text-primary text-sm uppercase tracking-wider">Historial de Cobranza</h3>
                    <p class="text-xs text-on-surface-variant font-medium">{{ $customer_name }}</p>
                </div>
                <button type="button" wire:click="closeHistoryModal" class="p-1 hover:bg-surface-container-high rounded-full"><span class="material-symbols-outlined">close</span></button>
            </div>

            <div class="p-6 overflow-y-auto space-y-3 flex-1 custom-scrollbar">
                @forelse($paymentHistory as $pay)
                    <div class="border border-outline-variant/60 rounded-xl p-3 bg-surface flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-[10px] px-2 py-0.5 bg-success/10 text-success rounded-full font-bold uppercase">
                                {{ $pay['payment_method'] === 'Bank Transfer' ? 'Pago Móvil' : ($pay['payment_method'] === 'Cash' ? 'Efectivo' : 'Punto') }}
                            </span>
                            <p class="text-xs font-data-mono text-outline">{{ \Carbon\Carbon::parse($pay['payment_date'])->format('Y-m-d H:i') }}</p>
                            @if($pay['notes'])
                                <p class="text-[11px] text-primary italic font-medium">Ref: {{ $pay['notes'] }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-bold font-data-mono text-primary text-sm">${{ number_format($pay['amount_usd'], 2) }}</p>
                            <p class="text-[10px] font-semibold text-secondary">A tasa: {{ number_format($pay['exchange_rate'], 2, ',', '.') }} Bs.</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-outline-variant space-y-2">
                        <span class="material-symbols-outlined text-4xl opacity-30">history_toggle_off</span>
                        <p class="text-xs font-medium">Esta cuenta aún no registra abonos amortizados.</p>
                    </div>
                @endforelse
            </div>
            <div class="p-4 bg-surface-container-low border-t border-outline-variant flex justify-end">
                <button type="button" wire:click="closeHistoryModal" class="px-5 py-1.5 bg-primary text-on-primary font-bold text-xs rounded-xl shadow-xs hover:opacity-90">
                    Cerrar Auditoría
                </button>
            </div>
        </div>
    </div>
</div>