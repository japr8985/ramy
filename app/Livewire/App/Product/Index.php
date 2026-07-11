<?php

namespace App\Livewire\App\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    // Propiedades de Búsqueda y Filtrado
    public $search = '';
    public $selectedCategory = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public array $kpi = ['total_products' => 0, 'critical_stock' => 0, 'stock_value' => 0, 'pending_orders' => 0];

    public bool $modal = false;

    public $barcode = '';
    public $name = '';
    public $category_id = '';
    public $quantity = '';
    public $purchase_price = '';
    public $exchangeRate = 0;
    public $selling_price = '';
    public $min_stock = '';
    
    protected $listeners = ['barcodeScanned' => 'loadProductByBarcode'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSelectedCategory() { $this->resetPage(); }
    public function mount(Product $product)
    {
        $this->exchangeRate = Product::getExchangeRate();
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
    public function save() 
{
    $this->validate([
        'barcode' => 'required|string|unique:products,sku',
        'name' => 'required|string',
        'purchase_price' => 'required|numeric',
        'selling_price' => 'required|numeric',
        'quantity' => 'required|integer',
        'min_stock' => 'required|integer',
    ], [
        'barcode.required' => 'El código de barras es obligatorio.',
        'barcode.unique' => 'Este código de barras ya está registrado.',
        'name.required' => 'El nombre del producto es obligatorio.',
        'category_id.required' => 'Debe seleccionar una categoría.',
        'purchase_price.required' => 'Indique el precio de compra.',
        'selling_price.required' => 'Indique el precio de venta.',
        'quantity.required' => 'El stock inicial es obligatorio.',
        'min_stock.required' => 'El stock mínimo es obligatorio.',
    ]);

    Product::create([
        'sku' => $this->barcode,
        'name' => $this->name,
        'category_id' => $this->category_id ?: \App\Models\Category::first()?->id, // Fallback temporal seguro
        'unit_id' => \App\Models\Unit::first()?->id, // Ajustar según tu lógica
        'purchase_price' => $this->purchase_price,
        'selling_price' => $this->selling_price,
        'quantity' => $this->quantity,
        'min_stock' => $this->min_stock,
    ]);

    $this->toggleModal(); // Cierra el modal de forma reactiva

    // ¡La llamada clave con parámetros con nombre!
    $this->dispatch('notify', title: '¡Éxito!', message: 'El producto ha sido guardado en el inventario.');
}
    public function loadProductByBarcode(string $scannedBarcode): void
    {
        $this->barcode = $scannedBarcode;

        // 1. Check your local inventory database first
        $product = Product::where('sku', $scannedBarcode)->first();

        if ($product) {
            $this->name = $product->name;
            $this->selling_price = $product->selling_price;
            $this->quantity = $product->quantity;
            $this->dispatch('notify', title: 'Producto Cargado', message: 'Los datos fueron tomados del inventario local.');
            return;
        }

        try {
            
            $response = Http::timeout(4)->get("https://world.openfoodfacts.org/api/v0/product/{$scannedBarcode}.json");

            if ($response->successful() && $response->json('status') == 1) {
                $this->dispatch('notify', title: 'Buscador Global', message: '¡Producto encontrado en internet!');
                $externalData = $response->json('product');
                // Automatically fill the name field with what we found on the web!
                $this->name = $externalData['product_name'] ?? $externalData['generic_name'] ?? '';
                $this->description = $externalData['brands'] ?? 'Importado vía API';

                $this->dispatch('notify', ['message' => '¡Producto encontrado en internet! Rellena el resto.']);
            } else {
                // Completely new barcode, no one knows what it is yet
                
                $this->resetFormExceptBarcode();
                $this->dispatch('notify', title: 'Código Nuevo', message: 'Ingrese los detalles manualmente.');
            }
        } catch (\Exception $e) {
            // Fallback gracefully if internet is slow or API is down
            $this->dispatch('notify', ['message' => 'No se pudo conectar al buscador global. Ingrese los datos manualmente.']);
        }
    }
    public function resetForm(): void
    {
        $this->reset(['barcode', 'name', 'category_id', 'quantity', 'selling_price', 'min_stock']);
    }
    public function toggleModal(): void
    {
        $this->modal = !$this->modal;
        if (!$this->modal) {
            $this->resetForm(); // Clean up fields when modal closes
        }
    }
    public function render()
    {
        

        $this->kpi['total_products'] = Product::count('id');

        $this->kpi['critical_stock'] = Product::whereColumn('quantity', '<', 'min_stock')->get()->count();

        $this->kpi['stock_value'] = Product::select(DB::raw('SUM(selling_price * quantity) as total_value'))
            ->where('quantity', '>', 0)
            ->value('total_value') ?? 0;

        $categories =  \App\Models\Category::orderBy('name')->get();

        $productsQuery = Product::with('category')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%') // ilike para PostgreSQL (Insensitive case)
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.app.product.index')->with([
            'kpi' => $this->kpi,
            'products' => $productsQuery->paginate(10),
            'categories' => $categories
        ]);
    }
}
