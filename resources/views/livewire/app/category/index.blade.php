<div class="p-margin-mobile md:p-gutter space-y-6">
    
    <!-- Page Header -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-headline-lg font-headline-lg text-primary tracking-tight">Categorías de Productos</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Estructure y clasifique los artículos de su inventario para agilizar la búsqueda en el POS.</p>
        </div>
        <button wire:click="create"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-lg font-semibold text-body-sm hover:opacity-90 active:scale-95 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Nueva Categoría
        </button>
    </section>

    <!-- Filtro de Búsqueda -->
    <div class="flex flex-col md:flex-row items-center gap-4">
        <div class="relative w-full md:max-w-md">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline">search</span>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm focus:ring-2 focus:ring-secondary" placeholder="Buscar categoría por nombre..." />
        </div>
    </div>

    <!-- Data Table de Categorías -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-xs overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant select-none text-xs">
                        <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant cursor-pointer" wire:click="sortBy('name')">
                            Nombre de la Categoría @if($sortField === 'name') {{$sortDirection === 'asc' ? '▲':'▼'}} @endif
                        </th>
                        <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant">Descripción / Observaciones</th>
                        <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant text-center" wire:click="sortBy('products_count')">
                            Productos Vinculados @if($sortField === 'products_count') {{$sortDirection === 'asc' ? '▲':'▼'}} @endif
                        </th>
                        <th class="px-6 py-4 text-label-caps font-label-caps text-on-surface-variant text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant text-body-sm">
                    @forelse($categories as $category)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-primary">
                                {{ $category->name }}
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant max-w-xs truncate" title="{{ $category->description }}">
                                {{ $category->description ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-data-mono font-bold text-xs {{ $category->products_count > 0 ? 'bg-secondary-fixed text-secondary' : 'bg-surface-container-high text-outline-variant' }}">
                                    {{ $category->products_count }} SKUs
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit('{{ $category->id }}')" class="p-1.5 text-outline hover:text-secondary rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-md">edit</span>
                                    </button>
                                    <button wire:click="delete('{{ $category->id }}')" wire:confirm="¿Desea eliminar esta categoría?" class="p-1.5 text-outline hover:text-error rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-md">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-on-surface-variant">No se encontraron categorías registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Modal Formulario (Crear / Editar) -->
    <div class="{{ $this->modal ? '' : 'hidden' }} fixed inset-0 z-50 flex items-end md:items-center justify-center" id="categoryModal">
        <div class="modal-overlay absolute inset-0 bg-black/40 backdrop-blur-xs" wire:click="toggleModal()"></div>

        <div class="relative bg-surface-container-lowest w-full md:max-w-md h-[92vh] md:h-auto rounded-t-2xl md:rounded-2xl shadow-2xl overflow-hidden border-t md:border border-outline-variant transition-all transform animate-in slide-in-from-bottom md:slide-in-from-bottom-0 md:zoom-in duration-200 z-10 flex flex-col">
            <div class="w-12 h-1.5 bg-outline-variant/60 rounded-full mx-auto my-3 md:hidden"></div>

            <div class="px-6 md:px-8 pb-4 md:py-6 border-b border-outline-variant flex items-center justify-between shrink-0">
                <h3 class="font-headline-lg text-headline-lg text-primary">{{ $isEditMode ? 'Editar Categoría' : 'Nueva Categoría' }}</h3>
                <button class="text-on-surface-variant hover:text-primary p-2" wire:click="toggleModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form wire:submit.prevent="save" class="px-6 md:px-8 py-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-[11px] font-label-caps text-primary font-bold uppercase">Nombre Comercial *</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-lg border @error('name') border-error ring-1 ring-error @else border-outline-variant @enderror bg-surface text-body-sm" placeholder="Ej: Electrónica, Víveres, Repuestos..." />
                    @error('name') <p class="text-error text-xs font-semibold mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-label-caps text-primary font-bold uppercase">Descripción Opcional</label>
                    <textarea wire:model="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-outline-variant bg-surface text-body-sm resize-none" placeholder="Breve reseña del grupo de productos..."></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-outline-variant/40">
                    <button class="px-5 py-2 text-on-surface-variant hover:text-primary font-semibold text-xs transition-colors" wire:click="toggleModal" type="button">Cancelar</button>
                    <button class="bg-primary text-on-primary px-6 py-2 rounded-xl font-bold text-xs hover:opacity-90 transition-all shadow-md" type="submit">Guardar Grupo</button>
                </div>
            </form>
        </div>
    </div>
</div>