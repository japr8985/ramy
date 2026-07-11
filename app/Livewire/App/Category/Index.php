<?php

namespace App\Livewire\App\Category;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filtros e Interfaz
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public bool $modal = false;
    public bool $isEditMode = false;
    public $selectedCategoryId = null;

    // Propiedades del Formulario
    public $name = '';
    public $description = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

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
        $this->reset(['name', 'description', 'isEditMode', 'selectedCategoryId']);
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
        $this->selectedCategoryId = $id;

        $category = Category::findOrFail($id);
        $this->name = $category->name;
        $this->description = $category->description;

        $this->toggleModal();
    }

    public function save()
    {
        $this->validate([
            // Condicionamos para que si es creación pase 'NULL' en lugar de un string vacío
            'name' => 'required|string|max:100|unique:categories,name,' . ($this->isEditMode ? $this->selectedCategoryId : 'NULL'),
            'description' => 'nullable|string|max:255'
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique' => 'Esta categoría ya se encuentra registrada.',
        ]);

        if ($this->isEditMode) {
            $category = Category::findOrFail($this->selectedCategoryId);
            $category->update([
                'name' => $this->name,
                'description' => $this->description ?: null,
            ]);
            $this->dispatch('notify', title: '¡Actualizada!', message: 'Categoría modificada con éxito.');
        } else {
            Category::create([
                'name' => $this->name,
                'description' => $this->description ?: null,
            ]);
            $this->dispatch('notify', title: '¡Éxito!', message: 'Nueva categoría comercial añadida.');
        }

        $this->toggleModal();
    }

    public function delete($id)
    {
        $category = Category::withCount('products')->findOrFail($id);

        // Seguridad: Impedir el borrado si hay stock amarrado a ella
        if ($category->products_count > 0) {
            $this->dispatch('notify', title: 'Acción Bloqueada', message: "No puedes eliminar una categoría que contiene {$category->products_count} productos asociados.");
            return;
        }

        $category->delete();
        $this->dispatch('notify', title: 'Eliminada', message: 'La categoría ha sido removida del sistema.');
    }

    public function render()
    {
        // Query reactivo con contador de productos amarrado por Eloquent
        $categoriesQuery = Category::withCount('products')
            ->when($this->search, function ($query) {
                $query->where('name', 'ilike', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.app.category.index')->with([
            'categories' => $categoriesQuery->paginate(10),
            'total_categories' => Category::count()
        ]);
    }
}
