<div class="max-w-4xl mx-auto p-margin-mobile md:p-gutter">
    
    <!-- Encabezado de la página -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('products') }}" class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-on-surface-variant">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">Editar Producto</h2>
            <p class="text-body-sm text-on-surface-variant">Modifique las especificaciones técnicas o costos del artículo.</p>
        </div>
    </div>

    <!-- Contenedor del Formulario -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 md:p-8 shadow-xs">
        <form wire:submit.prevent="save" class="space-y-6">
            
            <!-- Fila: Código de Barras y Nombre -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Código de Barras</label>
                    <input type="text" wire:model="barcode" class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md" required />
                    @error('barcode') <span class="text-error text-xs font-semibold">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Nombre del Producto</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md" required />
                    @error('name') <span class="text-error text-xs font-semibold">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Fila: Categoría y Unidad -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Categoría</label>
                    <select wire:model="category_id" class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md appearance-none">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Unidad de Medida</label>
                    <select wire:model="unit_id" class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md appearance-none">
                        <option value="">Seleccione una unidad</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Fila Financiera: Precios en $ y Conversión Informativa en Bs. -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-surface-container-low p-5 rounded-2xl border border-outline-variant/60">
                
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Precio de Compra ($)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">$</span>
                        <input type="number" step="0.0001" wire:model.live="purchase_price" class="w-full pl-8 pr-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md" required />
                    </div>
                    <!-- Conversión Dinámica en Pantalla -->
                    <p class="text-xs font-semibold text-secondary px-1">
                        Ref. Bs. {{ number_format((float)($purchase_price ?: 0) * $exchangeRate, 2, ',', '.') }} <span class="text-[10px] opacity-70">(Tasa: {{ $exchangeRate }})</span>
                    </p>
                </div>

                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Precio de Venta ($)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">$</span>
                        <input type="number" step="0.0001" wire:model.live="selling_price" class="w-full pl-8 pr-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md" required />
                    </div>
                    <!-- Conversión Dinámica en Pantalla -->
                    <p class="text-xs font-semibold text-secondary px-1">
                        Bs. {{ number_format((float)($selling_price ?: 0) * $exchangeRate, 2, ',', '.') }} <span class="text-[10px] opacity-70">(Tasa: {{ $exchangeRate }})</span>
                    </p>
                </div>

            </div>

            <!-- Fila: Stock e Inventario -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Stock Actual</label>
                    <input type="number" wire:model="quantity" class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md" required />
                </div>
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Stock Mínimo (Alerta Crítica)</label>
                    <input type="number" wire:model="min_stock" class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md" required />
                </div>
            </div>

            <!-- Campo Extendido: Descripción -->
            <div class="space-y-2">
                <label class="font-label-caps text-label-caps text-primary uppercase">Descripción del Producto</label>
                <textarea wire:model="description" rows="3" class="w-full px-4 py-3 bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary text-body-md" placeholder="Detalles o especificaciones adicionales..."></textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-6 border-t border-outline-variant flex items-center justify-end gap-4">
                <a href="{{ route('products') }}" class="px-6 py-2.5 text-on-surface-variant hover:text-primary font-semibold transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-primary text-on-primary px-8 py-2.5 rounded-xl font-bold hover:opacity-90 transition-all shadow-md">
                    Actualizar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
