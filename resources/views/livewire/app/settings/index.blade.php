<div class="p-margin-mobile md:p-gutter max-w-5xl mx-auto w-full">
    
    <!-- Encabezado del Módulo -->
    <header class="mb-10">
        <h2 class="text-headline-xl font-headline-xl text-primary">Configuración del Negocio</h2>
        <p class="text-body-md text-on-surface-variant">Gestione la identidad legal de su organización, indicadores financieros y el diseño de facturación.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Columna Izquierda: Formulario Principal -->
        <div class="lg:col-span-8 space-y-6">
            <form wire:submit.prevent="save" class="space-y-6">
                
                <!-- Tarjeta 1: Identidad -->
                <section class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-xs">
                    <div class="flex items-center gap-2 mb-6 text-secondary">
                        <span class="material-symbols-outlined">corporate_fare</span>
                        <h3 class="font-semibold text-headline-lg">Identidad & Legal</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-label-caps font-label-caps text-on-surface-variant block">Nombre de la Empresa *</label>
                            <input type="text" wire:model="business_name" class="w-full px-4 py-2.5 rounded-lg border @error('business_name') border-error @else border-outline-variant @enderror bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all" placeholder="Ej. Nexus Logistics C.A." />
                            @error('business_name') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-label-caps font-label-caps text-on-surface-variant block">RIF / Identificación Fiscal *</label>
                            <input type="text" wire:model="tax_id" class="w-full px-4 py-2.5 rounded-lg border @error('tax_id') border-error @else border-outline-variant @enderror bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all" placeholder="Ej. J-12345678-9" />
                            @error('tax_id') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <!-- Tarjeta 2: Valores Financieros -->
                <section class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-xs">
                    <div class="flex items-center gap-2 mb-6 text-secondary">
                        <span class="material-symbols-outlined">payments</span>
                        <h3 class="font-semibold text-headline-lg">Parámetros de Moneda</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-label-caps font-label-caps text-on-surface-variant block">Moneda Base del Sistema</label>
                            <select wire:model="currency" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all appearance-none">
                                <option value="USD">USD - Dólar Estadounidense</option>
                                <option value="VES">VES - Bolívar Soberano</option>
                                <option value="EUR">EUR - Euro</option>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-label-caps font-label-caps text-on-surface-variant block">Tasa de Cambio Oficial (BCV)</label>
                            <div class="relative">
                                <input type="number" step="0.01" wire:model="exchange_rate" class="w-full px-4 py-2.5 rounded-lg border @error('exchange_rate') border-error @else border-outline-variant @enderror bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all" />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-outline-variant material-symbols-outlined text-sm">sync_alt</span>
                            </div>
                            @error('exchange_rate') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <!-- Tarjeta 3: Configuración de Factura -->
                <section class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-xs">
                    <div class="flex items-center gap-2 mb-6 text-secondary">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <h3 class="font-semibold text-headline-lg">Pie de Recibo y Facturación</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-label-caps font-label-caps text-on-surface-variant block">Mensaje Legal o Agradecimiento</label>
                            <textarea wire:model.live="invoice_footer" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all resize-none" placeholder="Gracias por su compra. Condición de pago: Sujeto a cambio según tasa oficial..." rows="4"></textarea>
                            <p class="text-[11px] text-on-surface-variant/60 italic">Este texto se incrustará al final de los comprobantes impresos y PDFs.</p>
                        </div>
                    </div>
                </section>

                <!-- Botones de Acción del Formulario -->
                <div class="flex items-center justify-end gap-4 py-4">
                    <button type="button" wire:click="discardChanges" class="px-6 py-2.5 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all font-semibold">
                        Descartar
                    </button>
                    <button type="submit" class="px-8 py-2.5 bg-primary text-on-primary rounded-lg font-semibold hover:opacity-90 active:scale-95 transition-all flex items-center gap-2 shadow-md">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Guardar Configuración
                    </button>
                </div>
            </form>
        </div>

        <!-- Columna Derecha: Información de Contexto & Vista previa -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Vista Previa de Factura en Tiempo Real -->
            <div class="bg-primary-container text-on-primary-container p-6 rounded-xl shadow-lg relative overflow-hidden group">
                <div class="relative z-10">
                    <h4 class="font-bold text-headline-lg mb-2">Vista Previa</h4>
                    <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 border border-white/10 space-y-3">
                        <div class="flex justify-between items-center border-b border-white/5 pb-2">
                            <div class="w-8 h-8 rounded-full bg-secondary"></div>
                            <span class="text-[10px] font-mono tracking-tighter uppercase opacity-60">Ticket #00124</span>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[11px] font-bold text-white/90 truncate">{{ $business_name ?: 'Nombre de tu Empresa' }}</p>
                            <p class="text-[9px] text-white/60 truncate">RIF: {{ $tax_id ?: 'J-00000000-0' }}</p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-white/5">
                            <p class="text-[10px] text-white/50 italic line-clamp-2" title="{{ $invoice_footer }}">
                                {{ $invoice_footer ?: 'Gracias por su preferencia...' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-secondary opacity-20 blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            </div>

            <!-- Caja Bento de Ayuda Operativa -->
            <div class="bg-surface-container-high p-6 rounded-xl border border-outline-variant space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-surface-container-lowest flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined">help_center</span>
                    </div>
                    <h4 class="font-bold">Asistencia</h4>
                </div>
                <p class="text-body-sm text-on-surface-variant">La alteración de la tasa de cambio recalculará la visualización de los precios en Bolívares en los módulos de venta (POS) e inventario inmediatamente.</p>
            </div>

            <!-- Estatus de Sincronización del Sistema -->
            <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-xl flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <span class="text-label-caps font-label-caps">Servidor de Datos</span>
                    <span class="flex items-center gap-1 text-[11px] text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-full uppercase">
                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                        Conectado
                    </span>
                </div>
                <div class="h-1.5 w-full bg-surface-container-high rounded-full overflow-hidden">
                    <div class="h-full w-full bg-secondary"></div>
                </div>
                <p class="text-[10px] text-on-surface-variant">Tasa BCV cargada activamente en memoria de caché local.</p>
            </div>
        </div>
    </div>

    <!-- Toast de Notificación Exclusivo de Livewire (Auto-ocultable) -->
    <div x-data="{ open: @entangle('showToast') }" 
         x-show="open" 
         x-init="$watch('open', value => { if(value) { setTimeout(() => open = false, 4000) } })"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-24 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-24 opacity-0"
         class="fixed bottom-8 right-8 bg-inverse-surface text-inverse-on-surface px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 z-50"
         style="display: none;">
        <span class="material-symbols-outlined text-green-400">check_circle</span>
        <div>
            <p class="font-bold text-sm">Configuración Guardada</p>
            <p class="text-xs opacity-80">Los parámetros comerciales han sido actualizados con éxito.</p>
        </div>
    </div>
</div>
