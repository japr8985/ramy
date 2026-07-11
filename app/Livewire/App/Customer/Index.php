<?php

namespace App\Livewire\App\Customer;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Búsqueda y filtros
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Estados de Modales
    public bool $modal = false;
    public bool $isEditMode = false;
    public $selectedCustomerId = null;

    // Propiedades del Formulario
    public $rif_ci = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $type = 'Individual';
    public $notes = '';

    protected $listeners = ['refreshCustomers' => '$refresh'];

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
        if (!$this->modal) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'rif_ci', 'name', 'email', 'phone', 'address', 'type', 'notes', 'isEditMode', 'selectedCustomerId'
        ]);
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
        $this->selectedCustomerId = $id;

        $customer = Customer::findOrFail($id);
        $this->rif_ci = $customer->rif_ci;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->type = $customer->type;
        $this->notes = $customer->notes;

        $this->toggleModal();
    }

    public function save()
    {
        $rules = [
            'rif_ci' => 'required|string|max:50|unique:customers,rif_ci,' . $this->selectedCustomerId,
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:customers,email,' . $this->selectedCustomerId,
            'phone' => 'nullable|string|max:50',
            'type' => 'required|string|in:Individual,Corporate',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        $messages = [
            'rif_ci.required' => 'La Cédula o RIF es un campo obligatorio.',
            'rif_ci.unique' => 'Este RIF o Cédula ya se encuentra registrado.',
            'name.required' => 'El nombre es obligatorio.',
            'email.email' => 'Indique un formato de correo válido.',
            'email.unique' => 'Este correo ya pertenece a otro cliente.',
        ];

        $this->validate($rules, $messages);

        if ($this->isEditMode) {
            $customer = Customer::findOrFail($this->selectedCustomerId);
            $customer->update([
                'rif_ci' => $this->rif_ci,
                'name' => $this->name,
                'email' => $this->email ?: null,
                'phone' => $this->phone ?: null,
                'address' => $this->address ?: null,
                'type' => $this->type,
                'notes' => $this->notes ?: null,
            ]);

            $this->dispatch('notify', title: '¡Éxito!', message: 'Cliente actualizado correctamente.');
        } else {
            Customer::create([
                'rif_ci' => $this->rif_ci,
                'name' => $this->name,
                'email' => $this->email ?: null,
                'phone' => $this->phone ?: null,
                'address' => $this->address ?: null,
                'type' => $this->type,
                'notes' => $this->notes ?: null,
            ]);

            $this->dispatch('notify', title: '¡Éxito!', message: 'Cliente guardado correctamente.');
        }

        $this->toggleModal();
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        $this->dispatch('notify', title: 'Eliminado', message: 'Cliente movido a la papelera.');
    }

    public function render()
    {
        $customersQuery = Customer::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhere('rif_ci', 'ilike', '%' . $this->search . '%')
                      ->orWhere('email', 'ilike', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        // Bento KPI simplificado
        $stats = [
            'total_customers' => Customer::count(),
            // 'corporate_count' => Customer::where('type', 'Corporate')->count(),
            // 'individual_count' => Customer::where('type', 'Individual')->count(),
        ];

        return view('livewire.app.customer.index')->with([
            'customers' => $customersQuery->paginate(10),
            'stats' => $stats
        ]);
    }
}
