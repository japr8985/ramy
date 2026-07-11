<?php

namespace App\Livewire\App\Supplier;

use App\Models\Product;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filtros e Interfaz
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public bool $modal = false;
    public bool $isEditMode = false;
    public $selectedSupplierId = null;

    // Propiedades del Formulario
    public $name = '';
    public $contact_person = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $notes = '';
    public array $selectedProducts = []; // IDs de productos asociados

    public function updatingSearch() { $this->resetPage(); }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleModal(): void
    {
        $this->modal = !$this->modal;
        if (!$this->modal) $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'contact_person', 'email', 'phone', 'address', 'notes', 'selectedProducts', 'isEditMode', 'selectedSupplierId']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->toggleModal();
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->selectedSupplierId = $id;

        $supplier = Supplier::with('products')->findOrFail($id);
        $this->name = $supplier->name;
        $this->contact_person = $supplier->contact_person;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        $this->notes = $supplier->notes;
        
        // Cargar los IDs de los productos ya asociados
        $this->selectedProducts = $supplier->products->pluck('id')->toArray();

        $this->toggleModal();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email|max:150|unique:suppliers,email,' . ($this->isEditMode ? $this->selectedSupplierId : 'NULL'),
            'phone' => 'nullable|string|max:50',
            'selectedProducts' => 'nullable|array',
            'selectedProducts.*' => 'exists:products,id'
        ], [
            'name.required' => 'El nombre de la empresa proveedora es obligatorio.',
            'contact_person.required' => 'Debe indicar una persona de contacto.',
            'email.email' => 'Indique un correo electrónico válido.',
            'email.unique' => 'Este correo ya pertenece a otro proveedor.',
        ]);

        if ($this->isEditMode) {
            $supplier = Supplier::findOrFail($this->selectedSupplierId);
            $supplier->update([
                'name' => $this->name,
                'contact_person' => $this->contact_person,
                'email' => $this->email ?: null,
                'phone' => $this->phone ?: null,
                'address' => $this->address ?: null,
                'notes' => $this->notes ?: null,
            ]);
            
            // Sincronizar la tabla pivote
            $supplier->products()->sync($this->selectedProducts);
            $this->dispatch('notify', title: '¡Actualizado!', message: 'Proveedor modificado con éxito.');
        } else {
            $supplier = Supplier::create([
                'name' => $this->name,
                'contact_person' => $this->contact_person,
                'email' => $this->email ?: null,
                'phone' => $this->phone ?: null,
                'address' => $this->address ?: null,
                'notes' => $this->notes ?: null,
            ]);

            $supplier->products()->sync($this->selectedProducts);
            $this->dispatch('notify', title: '¡Éxito!', message: 'Nuevo proveedor añadido a la cadena de suministro.');
        }

        $this->toggleModal();
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        $this->dispatch('notify', title: 'Eliminado', message: 'El proveedor ha sido archivado suavemente.');
    }

    public function render()
    {
        $suppliersQuery = Supplier::with('products')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('contact_person', 'ilike', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        // Stats dinámicos basados en BD
        $stats = [
            'total_suppliers' => Supplier::count(),
            'total_products_linked' => \DB::table('product_supplier')->distinct('product_id')->count(),
        ];

        return view('livewire.app.supplier.index')->with([
            'suppliers' => $suppliersQuery->paginate(10),
            'allProducts' => Product::orderBy('name')->get(),
            'stats' => $stats
        ]);
    }
}
