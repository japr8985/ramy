<div class="p-margin-mobile md:p-gutter space-y-6">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-headline-xl font-headline-xl text-primary">Clientes</h2>
            <p class="text-body-md text-on-surface-variant">Gestione sus relaciones comerciales, directorio de contactos y datos de facturación.</p>
        </div>
        <button class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 shadow-sm hover:opacity-90 active:scale-[0.98] transition-all"
            wire:click="create()">
            <span class="material-symbols-outlined">person_add</span>
            Registrar Cliente
        </button>
    </div>

    <!-- Bento Statistics (Segmentación de Clientes) -->
    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
        <div class="bg-surface-container-lowest border border-outline-variant p-5 rounded-xl">
            <p class="text-label-caps font-label-caps text-on-surface-variant">Total Clientes</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-headline-lg font-headline-lg">{{ number_format($stats['total_customers']) }}</span>
                <span class="text-secondary text-label-caps font-label-caps">Registrados</span>
            </div>
        </div>
    </div>

    <!-- Buscador Integrado -->
    <div class="flex flex-col md:flex-row items-center gap-4">
        <div class="relative w-full md:max-w-md">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline">search</span>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm focus:ring-2 focus:ring-secondary" placeholder="Buscar por RIF/Cédula, nombre o correo..." />
        </div>
    </div>

    <!-- Tabla Adaptada y Limpia -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-6 py-3 text-label-caps font-label-caps text-on-surface-variant cursor-pointer" wire:click="sortBy('rif_ci')">
                            Identificación @if($sortField === 'rif_ci') {{$sortDirection === 'asc' ? '▲':'▼'}} @endif
                        </th>
                        <th class="px-6 py-3 text-label-caps font-label-caps text-on-surface-variant cursor-pointer" wire:click="sortBy('name')">
                            Nombre / Razón Social @if($sortField === 'name') {{$sortDirection === 'asc' ? '▲':'▼'}} @endif
                        </th>
                        <th class="px-6 py-3 text-label-caps font-label-caps text-on-surface-variant">Tipo</th>
                        <th class="px-6 py-3 text-label-caps font-label-caps text-on-surface-variant">Teléfono</th>
                        <th class="px-6 py-3 text-label-caps font-label-caps text-on-surface-variant">Email</th>
                        <th class="px-6 py-3 text-label-caps font-label-caps text-on-surface-variant text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-surface-container transition-colors group">
                            <td class="px-6 py-4 text-data-mono font-data-mono text-on-surface-variant">
                                {{ $customer->rif_ci }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-secondary-fixed text-secondary flex items-center justify-center font-bold text-xs">
                                        {{ $customer->initials }}
                                    </div>
                                    <span class="font-semibold">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $customer->type === 'Corporate' ? 'bg-primary-container text-primary-fixed-dim' : 'bg-surface-container-highest text-on-surface' }}">
                                    {{ $customer->type === 'Corporate' ? 'Jurídico' : 'Personal' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-body-sm">
                                <a href="tel:+{{ $customer->phone ?? '#' }}">{{ $customer->phone ?? '—' }}</a>
                            </td>
                            <td class="px-6 py-4 text-body-sm">
                                <a href="mailto:{{ $customer->email ?? '#' }}">{{ $customer->email ?? '—' }}</a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit('{{ $customer->id }}')" class="p-1 hover:text-secondary transition-all">
                                        <span class="material-symbols-outlined text-body-md">edit</span>
                                    </button>
                                    <button wire:click="delete('{{ $customer->id }}')" wire:confirm="¿Seguro que desea eliminar este cliente?" class="p-1 hover:text-error transition-all">
                                        <span class="material-symbols-outlined text-body-md">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-on-surface-variant text-body-sm">
                                No se encontraron clientes en el directorio.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Paginador -->
        <div class="p-4 bg-surface-container-low border-t border-outline-variant">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Modal Formulario Unificado (Crear / Editar) -->
    <div class="{{ $this->modal ? '' : 'hidden' }} fixed inset-0 z-50 flex items-end md:items-center justify-center" id="customerModal">
        <div class="modal-overlay absolute inset-0 bg-black/40 backdrop-blur-xs" wire:click="toggleModal()"></div>
        
        <div class="relative bg-surface-container-lowest w-full md:max-w-xl h-[92vh] md:h-auto rounded-t-2xl md:rounded-2xl shadow-2xl overflow-hidden border-t md:border border-outline-variant transition-all transform animate-in slide-in-from-bottom md:slide-in-from-bottom-0 md:zoom-in duration-200 z-10 flex flex-col">
            <div class="w-12 h-1.5 bg-outline-variant/60 rounded-full mx-auto my-3 md:hidden"></div>

            <div class="px-6 md:px-8 pb-4 md:py-6 border-b border-outline-variant flex justify-between items-center shrink-0">
                <h3 class="text-headline-lg font-headline-lg text-primary">{{ $isEditMode ? 'Editar Cliente' : 'Registrar Cliente' }}</h3>
                <button class="p-2 hover:bg-surface-container rounded-full" wire:click="toggleModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form wire:submit.prevent="save" class="p-6 md:p-8 space-y-4 overflow-y-auto flex-1 pb-12 md:pb-8">
                
                <!-- RIF o Cédula Venezolana -->
                <div class="space-y-1">
                    <label class="text-label-caps font-label-caps text-on-surface-variant">Cédula o RIF *</label>
                    <input class="w-full rounded-lg border @error('rif_ci') border-error @else border-outline-variant @enderror focus:ring-secondary focus:border-secondary"
                        type="text" wire:model="rif_ci" placeholder="Ej: J-12345678-9 o V-12345678" />
                    @error('rif_ci') <p class="text-error text-[11px] font-medium mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nombre Completo -->
                <div class="space-y-1">
                    <label class="text-label-caps font-label-caps text-on-surface-variant">Nombre o Razón Social *</label>
                    <input class="w-full rounded-lg border @error('name') border-error @else border-outline-variant @enderror focus:ring-secondary focus:border-secondary"
                        type="text" wire:model="name" />
                    @error('name') <p class="text-error text-[11px] font-medium mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- grid: Teléfono y Correo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-label-caps font-label-caps text-on-surface-variant">Número de Teléfono</label>
                        <input class="w-full rounded-lg border-outline-variant focus:ring-secondary focus:border-secondary"
                            type="text" wire:model="phone" placeholder="0414-1234567" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-label-caps font-label-caps text-on-surface-variant">Correo Electrónico</label>
                        <input class="w-full rounded-lg border @error('email') border-error @else border-outline-variant @enderror focus:ring-secondary focus:border-secondary"
                            type="email" wire:model="email" />
                        @error('email') <p class="text-error text-[11px] font-medium mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Tipo de Cliente -->
                <div class="space-y-1">
                    <label class="text-label-caps font-label-caps text-on-surface-variant">Tipo de Cliente</label>
                    <select class="w-full rounded-lg border-outline-variant focus:ring-secondary focus:border-secondary" wire:model="type">
                        <option value="Individual">Personal (Individual)</option>
                        <option value="Corporate">Jurídico (Corporate)</option>
                    </select>
                </div>

                <!-- Dirección de Habitación/Despacho -->
                <div class="space-y-1">
                    <label class="text-label-caps font-label-caps text-on-surface-variant">Dirección de Entrega / Habitación</label>
                    <input class="w-full rounded-lg border-outline-variant focus:ring-secondary focus:border-secondary"
                        type="text" wire:model="address" placeholder="Av. Principal, Edificio Alfa..." />
                </div>

                <!-- Notas -->
                <div class="space-y-1">
                    <label class="text-label-caps font-label-caps text-on-surface-variant">Notas u Observaciones</label>
                    <textarea class="w-full rounded-lg border-outline-variant focus:ring-secondary focus:border-secondary resize-none"
                        rows="2" wire:model="notes"></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button class="px-6 py-2.5 text-on-surface-variant font-semibold hover:bg-surface-container rounded-lg transition-colors"
                        wire:click="toggleModal()" type="button">Cancelar</button>
                    <button class="px-6 py-2.5 bg-primary text-on-primary font-semibold rounded-lg shadow-sm hover:opacity-90 active:scale-95 transition-all"
                        type="submit">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>