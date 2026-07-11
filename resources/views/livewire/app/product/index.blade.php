<div>
    <div class="p-gutter overflow-y-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="font-headline-lg text-headline-lg text-primary">Inventory Management</h2>
                <p class="text-body-sm text-on-surface-variant">Monitor and adjust your commercial stock levels
                    in real-time.</p>
            </div>
            <button
                class="flex items-center gap-2 bg-secondary text-on-secondary px-6 py-2.5 rounded-lg font-semibold hover:opacity-90 transition-opacity shadow-sm"
                wire:click="toggleModal()">
                <span class="material-symbols-outlined">add</span>
                <span>Nuevo Producto</span>
            </button>
        </div>
        <!-- Stats Overview (Bento Style) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-surface-container-lowest border border-outline-variant p-5 rounded-xl">
                <p class="text-label-caps text-on-surface-variant mb-1">Total de productos</p>
                <h3 class="text-headline-lg font-headline-lg">
                    {{ $this->kpi['total_products'] }}
                </h3>
                <div class="mt-2 text-xs text-secondary flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +12 this week
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant p-5 rounded-xl">
                <p class="text-label-caps text-on-surface-variant mb-1">Stock Critico</p>
                <h3 class="text-headline-lg font-headline-lg text-error">
                    {{ $this->kpi['critical_stock'] }}
                </h3>
                <div class="mt-2 text-xs text-error flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">warning</span> Action required
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant p-5 rounded-xl">
                <p class="text-label-caps text-on-surface-variant mb-1">Stock Value</p>
                <h3 class="text-headline-lg font-headline-lg">
                    Bs. {{ number_format($this->kpi['stock_value'], 2, ',', '.') }}
                </h3>
                <div class="mt-2 text-xs text-on-surface-variant">Estimated retail total</div>
            </div>
        </div>
        <!-- Filter Row -->
        <!-- Barra de Filtros Activa -->
    <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
        <!-- Buscador en tiempo real -->
        <div class="relative w-full md:max-w-xs">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline">search</span>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm focus:ring-2 focus:ring-secondary" placeholder="Buscar por nombre o SKU..." />
        </div>

        <!-- Filtro por Categorías -->
        <div class="w-full md:w-48">
            <select wire:model.live="selectedCategory" class="w-full px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm appearance-none">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
        <!-- Data Table -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-x-auto">
            <table class="w-full min-w-[800px] text-left border-collapse">
                <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant select-none">
                    <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant cursor-pointer" wire:click="sortBy('sku')">
                        Código @if($sortField === 'sku') {{$sortDirection === 'asc' ? '▲':'▼'}} @endif
                    </th>
                    <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant cursor-pointer" wire:click="sortBy('name')">
                        Producto @if($sortField === 'name') {{$sortDirection === 'asc' ? '▲':'▼'}} @endif
                    </th>
                    <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant">Categoría</th>
                    <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant text-right cursor-pointer" wire:click="sortBy('quantity')">
                        Stock @if($sortField === 'quantity') {{$sortDirection === 'asc' ? '▲':'▼'}} @endif
                    </th>
                    <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant text-right cursor-pointer" wire:click="sortBy('selling_price')">
                        Precio ($) @if($sortField === 'selling_price') {{$sortDirection === 'asc' ? '▲':'▼'}} @endif
                    </th>
                    <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant text-right">Precio (Bs.)</th>
                    <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant text-center">Acciones</th>
                </tr>
            </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    <!-- Row 1: Critical -->
                    @forelse ($products as $product)
                        <tr class="hover:bg-surface-container-lowest transition-colors group">
                            <td class="px-6 py-4 font-data-mono text-data-mono text-on-surface-variant">
                                {{ $product->sku }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-primary">
                                {{ $product->name }}
                            </td>
                            <td class="px-6 py-4 text-body-sm text-on-surface-variant">
                                {{ $product->category?->name ?? 'Sin Categoría' }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-error">
                                {{ $product->quantity }}
                            </td>
                            <td class="px-6 py-4 text-right text-body-sm">
                                Bs. {{ number_format($product->selling_price_ves, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($product->quantity <= $product->min_stock)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-error-container text-on-error-container">
                                        Critical
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-tertiary-fixed text-on-tertiary-fixed">
                                        In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('products.edit', $product->id) }}"
                                    class="p-1.5 text-outline hover:text-secondary">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-on-surface-variant text-body-sm">
                                No products found in inventory.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Dynamic Pagination Footer Navigation -->
            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <!-- Contenedor Principal del Modal -->
    <div class="{{ $this->modal ? '' : 'hidden' }} fixed inset-0 z-50 flex items-end md:items-center justify-center"
        id="productModal">

        <!-- Fondo oscuro con efecto Blur -->
        <div class="modal-overlay absolute inset-0 bg-black/40 backdrop-blur-xs" wire:click="toggleModal()"></div>

        <!-- Tarjeta del Modal (Animada y Adaptativa) -->
        <div
            class="relative bg-surface-container-lowest w-full md:max-w-2xl h-[92vh] md:h-auto rounded-t-2xl md:rounded-2xl shadow-2xl overflow-hidden border-t md:border border-outline-variant transition-all transform animate-in slide-in-from-bottom md:slide-in-from-bottom-0 md:zoom-in duration-200 z-10 flex flex-col">

            <!-- Línea decorativa visual para móviles (indica que se puede arrastrar/cerrar) -->
            <div class="w-12 h-1.5 bg-outline-variant/60 rounded-full mx-auto my-3 md:hidden"></div>

            <!-- Modal Header -->
            <div
                class="px-6 md:px-8 pb-4 md:py-6 border-b border-outline-variant flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-headline-lg text-headline-lg text-primary">Nuevo Producto</h3>
                    <p class="text-body-sm text-on-surface-variant">Ingrese los detalles del nuevo artículo.</p>
                </div>
                <button class="text-on-surface-variant hover:text-primary p-2" wire:click="toggleModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Camera Scanner Area (Hidden by default) -->
            <div id="scanner-container" class="hidden px-8 pt-6">
                <div class="relative bg-black rounded-xl overflow-hidden border border-outline-variant">
                    <div id="interactive-reader" class="w-full"></div>
                    <button type="button" id="btn-close-scanner"
                        class="absolute top-4 right-4 bg-error text-on-error px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 shadow-md z-10">
                        <span class="material-symbols-outlined text-sm">videocam_off</span>
                        Cerrar Cámara
                    </button>
                </div>
            </div>

            <!-- Formulario con scroll independiente en móvil -->
            <form wire:submit.prevent="save" class="px-6 md:px-8 py-6 space-y-6 overflow-y-auto flex-1 pb-12 md:pb-6" id="productForm">

                <!-- Barcode Section -->
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-primary uppercase">Código de Barras</label>
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                <input
                    class="w-full pl-4 pr-4 py-3 bg-surface @error('barcode') border-error ring-1 ring-error @else border-outline-variant @enderror rounded-xl focus:ring-2 focus:ring-secondary text-body-md border focus:border-secondary transition-all"
                    id="barcode" placeholder="Escanee o ingrese código" wire:model="barcode" type="text" />
                @error('barcode') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
            </div>
                        <!-- Camera Trigger Button -->
                        <button id="btn-start-scanner"
                            class="flex flex-col items-center justify-center bg-secondary text-on-secondary px-6 rounded-xl hover:opacity-90 transition-opacity min-w-[100px]"
                            type="button">
                            <span class="material-symbols-outlined text-[32px] mb-1">barcode_scanner</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider">Escanear</span>
                        </button>
                    </div>
                </div>

                <!-- Nombre y Categoría -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="font-label-caps text-label-caps text-primary uppercase">Nombre del Producto</label>
            <input
                class="w-full px-4 py-3 bg-surface @error('name') border-error ring-1 ring-error @else border-outline-variant @enderror rounded-xl focus:ring-2 focus:ring-secondary text-body-md border"
                id="productName" placeholder="Nombre descriptivo" type="text" wire:model="name" />
            @error('name') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
        </div>
        
        <div class="space-y-2">
            <label class="font-label-caps text-label-caps text-primary uppercase">Categoría</label>
            <select wire:model="category_id"
                class="w-full px-4 py-3 bg-surface @error('category_id') border-error ring-1 ring-error @else border-outline-variant @enderror border rounded-xl focus:ring-2 focus:ring-secondary text-body-md appearance-none">
                <option value="">Seleccione una categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
                <hr>
                <!-- Fila Financiera: Precios en $ y Conversión Informativa en Bs. -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-surface-container-low p-5 rounded-2xl border border-outline-variant/60">
        <div class="space-y-2">
            <label class="font-label-caps text-label-caps text-primary uppercase">Precio de Compra ($)</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">$</span>
                <input type="number" step="0.0001" wire:model.live="purchase_price"
                    class="w-full pl-8 pr-4 py-3 bg-surface-container-lowest @error('purchase_price') border-error ring-1 ring-error @else border-outline-variant @enderror border rounded-xl focus:ring-2 focus:ring-secondary text-body-md" />
            </div>
            @error('purchase_price') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
            <p class="text-xs font-semibold text-secondary px-1 mt-1">
                Ref. Bs. {{ number_format((float) ($purchase_price ?: 0) * $exchangeRate, 2, ',', '.') }}
            </p>
        </div>

        <div class="space-y-2">
            <label class="font-label-caps text-label-caps text-primary uppercase">Precio de Venta ($)</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">$</span>
                <input type="number" step="0.0001" wire:model.live="selling_price"
                    class="w-full pl-8 pr-4 py-3 bg-surface-container-lowest @error('selling_price') border-error ring-1 ring-error @else border-outline-variant @enderror border rounded-xl focus:ring-2 focus:ring-secondary text-body-md" />
            </div>
            @error('selling_price') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
            <p class="text-xs font-semibold text-secondary px-1 mt-1">
                Bs. {{ number_format((float) ($selling_price ?: 0) * $exchangeRate, 2, ',', '.') }}
            </p>
        </div>
    </div>
                <!-- Stocks e Inventario -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="font-label-caps text-label-caps text-primary uppercase">Stock Actual</label>
            <input type="number" wire:model="quantity"
                class="w-full px-4 py-3 bg-surface @error('quantity') border-error ring-1 ring-error @else border-outline-variant @enderror border rounded-xl focus:ring-2 focus:ring-secondary text-body-md" />
            @error('quantity') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2">
            <label class="font-label-caps text-label-caps text-primary uppercase">Stock Mínimo (Alerta Crítica)</label>
            <input type="number" wire:model="min_stock"
                class="w-full px-4 py-3 bg-surface @error('min_stock') border-error ring-1 ring-error @else border-outline-variant @enderror border rounded-xl focus:ring-2 focus:ring-secondary text-body-md" />
            @error('min_stock') <p class="text-error text-xs font-semibold mt-1">{{ $message }}</p> @enderror
        </div>
    </div>



                <div class="pt-6 border-t border-outline-variant flex items-center justify-end gap-4">
                    <button
                        class="px-6 py-2.5 text-on-surface-variant hover:text-primary font-semibold transition-colors"
                        wire:click="toggleModal()" type="button">Cancelar</button>
                    <button
                        class="bg-primary text-on-primary px-8 py-2.5 rounded-xl font-bold hover:opacity-90 transition-all shadow-md"
                        type="submit">Guardar Producto</button>
                </div>

            </form>
        </div>
    </div>


    <script>
        document.addEventListener('livewire:navigated', () => {
            let html5QrcodeScanner = null;
            const barcodeInput = document.getElementById('barcode');
            const scannerWrapper = document.getElementById('scanner-container');
            const startBtn = document.getElementById('btn-start-scanner');
            const closeBtn = document.getElementById('btn-close-scanner');

            function stopScanner() {
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    html5QrcodeScanner.stop().then(() => {
                        scannerWrapper.classList.add('hidden');
                    }).catch(err => console.error("Failed to stop scanner:", err));
                } else {
                    scannerWrapper.classList.add('hidden');
                }
            }

            startBtn.addEventListener('click', () => {
                scannerWrapper.classList.remove('hidden');

                // Initialize scanner instance targeting the element ID
                html5QrcodeScanner = new Html5Qrcode("interactive-reader");

                const config = {
                    fps: 15,
                    qrbox: { width: 300, height: 150 }, // Aspect ratio optimized for long horizontal barcodes
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                };

                html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        // 1. Send the data to your Livewire Component directly
                        Livewire.dispatch('barcodeScanned', { scannedBarcode: decodedText });

                        // 2. Turn off the active camera stream overlay cleanly
                        stopScanner();
                    },
                    (errorMessage) => { /* Keep empty */ }
                );


            });

            closeBtn.addEventListener('click', stopScanner);

            // Ensure camera stops cleanly if the modal loses visibility or toggles
            window.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal-overlay')) {
                    stopScanner();
                }
            });

            
        });
    </script>

</div>