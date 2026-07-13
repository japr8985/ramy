<div class="flex-1 flex flex-col overflow-hidden min-h-[calc(100vh-4rem)] relative">
    
    <!-- BARRA FLOTANTE SUPERIOR (Solo Móvil): Indicador rápido del Carrito -->
    <div class="md:hidden flex items-center justify-between bg-surface-container-low border-b border-outline-variant p-4 shrink-0">
        <h2 class="text-title-md font-bold text-primary flex items-center gap-2">
            <span class="material-symbols-outlined">store</span>
            Nexus POS
        </h2>
        
        <button type="button" 
                wire:click="$toggle('showCart')"
                class="px-4 py-2 bg-secondary text-on-secondary rounded-xl text-xs font-bold flex items-center gap-2 relative shadow-md">
            <span class="material-symbols-outlined text-sm">shopping_cart</span>
            {{ $showCart ? 'Ver Catálogo' : 'Ver Carrito' }}
            @if(count($cart) > 0)
                <span class="absolute -top-1.5 -right-1.5 bg-error text-on-error w-5 h-5 rounded-full text-[10px] flex items-center justify-center font-bold font-data-mono">
                    {{ count($cart) }}
                </span>
            @endif
        </button>
    </div>

    <!-- CONTENEDOR FLEX PRINCIPAL -->
    <div class="flex-1 flex flex-row overflow-hidden relative w-full h-full">
        
        <!-- COLUMNA IZQUIERDA: Catálogo de Productos -->
        <section class="{{ $showCart ? 'hidden md:flex' : 'flex' }} flex-[1.5] p-4 md:p-6 overflow-y-auto flex-col space-y-6 border-r border-outline-variant w-full h-full pb-32 md:pb-6">
            
            <!-- Botón de Escaneo Adaptativo o Entrada de Cámara de Video -->
            @if($cameraActive)
                <div class="relative bg-black rounded-xl overflow-hidden border border-outline-variant p-4 shrink-0">
                    <div id="interactive-reader" class="w-full"></div>
                    <button type="button" wire:click="toggleCamera" class="absolute top-4 right-4 bg-error text-on-error px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 shadow-md z-10">
                        <span class="material-symbols-outlined text-sm">videocam_off</span>
                        Apagar Cámara
                    </button>
                </div>
            @else
                <button wire:click="toggleCamera"
                    class="w-full group relative overflow-hidden bg-primary text-on-primary py-5 md:py-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 active:scale-[0.98] flex flex-col items-center justify-center gap-1 shrink-0">
                    <span class="material-symbols-outlined text-4xl group-hover:scale-110 transition-transform duration-300">barcode_scanner</span>
                    <span class="text-[11px] font-bold tracking-wider uppercase">Activar Escáner</span>
                </button>
            @endif

            <!-- Barra de Búsqueda de Productos -->
            <div class="space-y-4 shrink-0">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h2 class="text-headline-md md:text-headline-lg font-headline-lg">Catálogo de Productos</h2>
                    <div class="relative w-full sm:w-64">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm">search</span>
                        <input type="text" wire:model.live.debounce.200ms="search" class="w-full pl-9 pr-4 py-1.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm focus:ring-2 focus:ring-secondary" placeholder="Buscar por nombre o código..." />
                    </div>
                </div>

                <!-- Cuadrícula Bento de Productos -->
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                    @foreach($catalog as $product)
                        <div wire:click="addToCart('{{ $product->id }}')"
                            class="group bg-surface-container-lowest border border-outline-variant p-3 md:p-4 rounded-xl hover:border-secondary transition-all cursor-pointer shadow-xs hover:shadow-md flex flex-col justify-between active:scale-95 duration-150">
                            <div>
                                <div class="aspect-square bg-surface-container-low rounded-lg mb-2 overflow-hidden flex items-center justify-center text-outline">
                                    <span class="material-symbols-outlined text-3xl opacity-40">inventory_2</span>
                                </div>
                                <h3 class="font-bold text-xs md:text-body-md truncate text-primary px-0.5">{{ $product->name }}</h3>
                                <p class="text-[9px] text-outline font-data-mono px-0.5">SKU: {{ $product->sku }}</p>
                            </div>
                            <div class="flex flex-col mt-2 pt-2 border-t border-outline-variant/30 px-0.5">
                                <span class="text-secondary font-data-mono font-bold text-xs md:text-sm">${{ number_format($product->selling_price, 2, ',', '.') }}</span>
                                <span class="text-[10px] font-semibold text-outline">Bs. {{ number_format($product->selling_price_ves, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- COLUMNA DERECHA: Resumen de Carrito -->
        <section class="{{ $showCart ? 'flex' : 'hidden md:flex' }} flex-1 bg-surface-container-low flex flex-col h-[calc(100vh-8rem)] md:h-full w-full border-t md:border-t-0 overflow-hidden z-20">
            
            <!-- Cabecera del Carrito -->
            <div class="p-4 md:p-6 border-b border-outline-variant flex justify-between items-center bg-surface-container-lowest shrink-0">
                <h2 class="text-body-lg md:text-headline-lg font-headline-lg flex items-center gap-2 text-primary">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    Venta Actual
                </h2>
                <button wire:click="clearCart" class="text-error text-label-caps hover:underline font-bold text-xs tracking-wider">VACIAR TODO</button>
            </div>

            <!-- Listado de Ítems del Carrito (FIXED: Ahora sí es escroleable de forma independiente y visible) -->
            <div class="flex-1 overflow-y-auto px-4 md:px-6 py-4 space-y-3 custom-scrollbar">
                @if(count($cart) === 0)
                    <div class="h-full flex flex-col items-center justify-center text-outline-variant p-8 text-center space-y-2 py-20">
                        <span class="material-symbols-outlined text-5xl opacity-40">add_shopping_cart</span>
                        <p class="text-xs md:text-sm font-medium">El carrito está vacío. Seleccione un artículo del catálogo.</p>
                    </div>
                @endif

                @foreach($cart as $item)
                    <div class="flex items-center justify-between gap-3 bg-surface-container-lowest p-3 rounded-xl border border-outline-variant shadow-xs">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-xs md:text-body-md truncate text-primary">{{ $item['name'] }}</p>
                            <p class="text-[11px] text-outline font-data-mono">1x ${{ number_format($item['price'], 2, ',', '.') }}</p>
                            <p class="text-[9px] font-semibold text-secondary">Ref: Bs. {{ number_format($item['price'] * $exchangeRate, 2, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="flex items-center border border-outline-variant rounded-lg bg-surface">
                                <button wire:click="updateQuantity('{{ $item['id'] }}', -1)" class="px-2 py-0.5 font-bold hover:bg-surface-container-high text-xs">-</button>
                                <span class="px-2.5 py-0.5 font-data-mono text-xs border-x border-outline-variant bg-surface-container-lowest">{{ $item['qty'] }}</span>
                                <button wire:click="updateQuantity('{{ $item['id'] }}', 1)" class="px-2 py-0.5 font-bold hover:bg-surface-container-high text-xs">+</button>
                            </div>
                            <div class="text-right min-w-[70px]">
                                <span class="block font-data-mono font-bold text-xs text-primary">${{ number_format($item['price'] * $item['qty'], 2, ',', '.') }}</span>
                                <span class="block text-[9px] font-semibold text-outline">Bs. {{ number_format(($item['price'] * $item['qty']) * $exchangeRate, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="relative p-4 md:p-6 bg-surface-container-highest border-t border-outline-variant space-y-4 shadow-2xl md:shadow-none shrink-0 bg-opacity-95 backdrop-blur-md pb-20 md:pb-6">
                
                <!-- Selector de Clientes Híbrido -->
                <div class="space-y-1">
                    <label class="text-label-caps font-bold text-outline text-[10px] block">ASOCIAR CLIENTE @if($payment_method === 'credit') <span class="text-error">*</span> @endif</label>
                    <div class="flex gap-2">
                        <select wire:model="selectedCustomerId" class="flex-1 px-3 py-2 bg-surface border border-outline-variant rounded-xl text-xs focus:ring-2 focus:ring-secondary">
                            <option value="">Cliente Genérico (Contado)</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->rif_ci }} — {{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="toggleCustomerModal" class="p-2 bg-secondary text-on-secondary rounded-xl hover:opacity-90 flex items-center justify-center">
                            <span class="material-symbols-outlined text-sm">person_add</span>
                        </button>
                    </div>
                </div>

                <!-- Totales y Descuento -->
                <div class="space-y-2 border-b border-outline-variant/60 pb-3">
                    <div class="flex justify-between text-xs text-on-surface-variant items-center">
                        <span>Subtotal ($)</span>
                        <span class="font-data-mono font-semibold">${{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-xs text-on-surface-variant items-center">
                        <span>Descuento ($)</span>
                        <div class="relative w-24">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-outline-variant">$</span>
                            <input type="number" step="0.01" wire:model.live="total_discount" class="w-full pl-5 pr-2 py-0.5 bg-surface border border-outline-variant rounded-lg font-data-mono text-xs text-right text-error focus:ring-1 focus:ring-error" placeholder="0.00" />
                        </div>
                    </div>

                    <div class="pt-2 flex justify-between text-primary border-t border-outline-variant/40 items-baseline">
                        <span class="font-bold text-xs uppercase text-outline">Total a Pagar</span>
                        <div class="text-right">
                            <span class="block font-data-mono text-lg md:text-2xl font-bold text-primary">${{ number_format($total, 2, ',', '.') }}</span>
                            <span class="block text-[11px] font-semibold text-secondary">Bs. {{ number_format($total * $exchangeRate, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Métodos de Pago -->
                <div class="space-y-1.5">
                    <div class="grid grid-cols-3 gap-2">
                        <button wire:click="selectPaymentMethod('cash')"
                            class="flex flex-col items-center justify-center py-2 px-1 rounded-xl border transition-all {{ $payment_method === 'cash' ? 'bg-secondary text-on-secondary border-secondary shadow-xs' : 'bg-surface-container-lowest text-primary border-outline-variant' }}">
                            <span class="material-symbols-outlined text-sm mb-0.5">payments</span>
                            <span class="text-[9px] font-bold">EFECTIVO</span>
                        </button>
                        <button wire:click="selectPaymentMethod('card')"
                            class="flex flex-col items-center justify-center py-2 px-1 rounded-xl border transition-all {{ $payment_method === 'card' ? 'bg-secondary text-on-secondary border-secondary shadow-xs' : 'bg-surface-container-lowest text-primary border-outline-variant' }}">
                            <span class="material-symbols-outlined text-sm mb-0.5">credit_card</span>
                            <span class="text-[9px] font-bold">PUNTO / PM</span>
                        </button>
                        <button wire:click="selectPaymentMethod('credit')"
                            class="flex flex-col items-center justify-center py-2 px-1 rounded-xl border transition-all {{ $payment_method === 'credit' ? 'bg-error text-on-error border-error shadow-xs' : 'bg-surface-container-lowest text-primary border-outline-variant' }}">
                            <span class="material-symbols-outlined text-sm mb-0.5">handshake</span>
                            <span class="text-[9px] font-bold">A CRÉDITO</span>
                        </button>
                    </div>
                </div>

                <!-- Botón de Confirmación -->
                <button wire:click="confirmSaleSubmission"
                        class="w-full bg-primary text-on-primary py-3 rounded-xl text-xs md:text-sm font-bold shadow-lg hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    <span>Procesar Transacción</span>
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                </button>
            </div>
        </section>
    </div>

    <!-- Micro-Modal de Registro Rápido de Clientes (FIXED: items-center permanente para centrado exacto) -->
    <div class="{{ $customerModal ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" wire:click="toggleCustomerModal"></div>
        <div class="relative bg-surface-container-lowest w-full max-w-md rounded-2xl border border-outline-variant shadow-2xl flex flex-col transform animate-in zoom-in duration-150 z-10">
            <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <h3 class="font-bold text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary">person_add</span> Cliente Exprés</h3>
                <button type="button" wire:click="toggleCustomerModal" class="p-1 hover:bg-surface-container-high rounded-full"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form wire:submit.prevent="saveQuickCustomer" class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-[11px] font-label-caps text-outline font-bold uppercase">Cédula o RIF *</label>
                    <input type="text" wire:model="quick_rif_ci" class="w-full px-4 py-2.5 rounded-lg border @error('quick_rif_ci') border-error ring-1 ring-error @else border-outline-variant @enderror bg-surface text-body-sm" placeholder="Ej: V-12345678 o J-31234567-8" />
                    @error('quick_rif_ci') <p class="text-error text-xs font-semibold mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-[11px] font-label-caps text-outline font-bold uppercase">Nombre o Razón Social *</label>
                    <input type="text" wire:model="quick_name" class="w-full px-4 py-2.5 rounded-lg border @error('quick_name') border-error ring-1 ring-error @else border-outline-variant @enderror bg-surface text-body-sm" placeholder="Nombre completo" />
                    @error('quick_name') <p class="text-error text-xs font-semibold mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-[11px] font-label-caps text-outline font-bold uppercase">Teléfono</label>
                    <input type="text" wire:model="quick_phone" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface text-body-sm" placeholder="Opcional" />
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" wire:click="toggleCustomerModal" class="px-4 py-2 text-on-surface-variant font-semibold text-xs hover:underline">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-primary text-on-primary font-bold text-xs rounded-lg shadow-sm hover:opacity-90 transition-opacity">Vincular Cliente</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Micro-Modal Intermedio: Parámetros del Crédito e Inicial (FIXED: items-center permanente para centrado exacto) -->
    <div class="{{ $creditConfirmModal ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" wire:click="$set('creditConfirmModal', false)"></div>
        
        <div class="relative bg-surface-container-lowest w-full max-w-md rounded-2xl border border-outline-variant shadow-2xl flex flex-col transform animate-in zoom-in duration-150 z-10">
            <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <h3 class="font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-error">handshake</span> 
                    Configuración del Crédito
                </h3>
                <button type="button" wire:click="$set('creditConfirmModal', false)" class="p-1 hover:bg-surface-container-high rounded-full"><span class="material-symbols-outlined">close</span></button>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- Resumen de Deuda Total -->
                <div class="bg-surface p-4 rounded-xl border border-outline-variant/60 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-bold text-outline uppercase tracking-wider">Importe Total de Venta</p>
                        <p class="text-xs font-semibold text-secondary">Bs. {{ number_format($total * $exchangeRate, 2, ',', '.') }}</p>
                    </div>
                    <span class="font-data-mono font-bold text-xl text-primary">${{ number_format($total, 2, ',', '.') }}</span>
                </div>

                <!-- Entrada de Inicial -->
                <div class="space-y-1">
                    <label class="text-[11px] font-label-caps text-outline font-bold uppercase block">Monto Inicial Entregado ($)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-outline-variant">$</span>
                        <input type="number" step="0.01" wire:model.live="initial_payment" class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest font-data-mono text-sm" placeholder="0.00" />
                    </div>
                    <p class="text-[10px] text-secondary font-medium px-1">
                        Equivalente: Bs. {{ number_format((float)($initial_payment ?: 0) * $exchangeRate, 2, ',', '.') }}
                    </p>
                </div>

                <!-- Remanente de deudas resultante -->
                <div class="pt-2 border-t border-outline-variant/40 flex justify-between items-center">
                    <div>
                        <span class="text-[11px] font-label-caps text-error font-bold uppercase block">Saldo Restante Financiado</span>
                        <span class="text-[10px] font-semibold text-outline-variant">Ref. Bs. {{ number_format($remaining_balance * $exchangeRate, 2, ',', '.') }}</span>
                    </div>
                    <span class="font-data-mono font-bold text-lg text-error">${{ number_format($remaining_balance, 2, ',', '.') }}</span>
                </div>

                <!-- Acciones del Modal -->
                <div class="pt-4 flex justify-end gap-3 border-t border-outline-variant/40">
                    <button type="button" wire:click="$set('creditConfirmModal', false)" class="px-4 py-2 text-on-surface-variant font-semibold text-xs hover:underline">Modificar Carrito</button>
                    <button type="button" wire:click="processSale" class="px-6 py-2.5 bg-error text-on-error font-bold text-xs rounded-lg shadow-md hover:opacity-90 active:scale-95 transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">assignment_turned_in</span>
                        Confirmar Crédito
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>