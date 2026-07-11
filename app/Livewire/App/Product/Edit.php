<?php

namespace App\Livewire\App\Product;

use Livewire\Component;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
class Edit extends Component
{
    public Product $product;
    public $barcode;
    public $name;
    public $category_id;
    public $unit_id;
    public $purchase_price;
    public $selling_price;
    public $quantity;
    public $min_stock;
    public $description;
    public $notes;
    public $exchangeRate;
    public function mount(Product $product)
    {
        $this->product = $product;
        $this->exchangeRate = Product::getExchangeRate();

        // Inicializar propiedades
        $this->barcode = $product->sku;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->unit_id = $product->unit_id;
        $this->purchase_price = $product->purchase_price;
        $this->selling_price = $product->selling_price;
        $this->quantity = $product->quantity;
        $this->min_stock = $product->min_stock;
        $this->description = $product->description;
        $this->notes = $product->notes;
    }
    public function save()
    {
        $this->validate([
            'barcode' => 'required|string|max:50|unique:products,sku,' . $this->product->id,
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        $this->product->update([
            'sku' => $this->barcode,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'unit_id' => $this->unit_id,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'quantity' => $this->quantity,
            'min_stock' => $this->min_stock,
            'description' => $this->description,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Producto actualizado con éxito.');
        return redirect()->route('products');
    }
    public function render()
    {
        return view('livewire.app.product.edit', [
            'categories' => Category::orderBy('name', 'ASC')->get(),
            'units' => Unit::orderBy('name', 'ASC')->get(),
        ]);
    }
}
