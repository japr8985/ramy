<?php

namespace App\Livewire\App\Sale;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    // Cesta de compras y catálogos
    public array $cart = [];
    public $search = '';
    public $selectedCustomerId = '';
    public $payment_method = 'cash'; // cash, card, credit
    public $notes = '';

    // Totales financieros base ($)
    public $subtotal = 0;
    public $total_discount = 0;
    public $total = 0;
    

    // Tasa BCV local
    public $exchangeRate = 1.0;

    // Control del Scanner de Cámara integrado
    public bool $cameraActive = false;

    // Estado del modal rápido de clientes y sus campos
    public bool $customerModal = false;
    public $quick_rif_ci = '';
    public $quick_name = '';
    public $quick_phone = '';

    // NUEVO: Modal Intermedio de Confirmación de Crédito e Inicial ($)
    public bool $creditConfirmModal = false;
    public $initial_payment = '0.00'; // Inicial dada por el cliente en $
    public $remaining_balance = 0;     // Remanente que irá a la cuenta por cobrar

    protected $listeners = ['barcodeScanned' => 'scanBarcode'];

    public function mount()
    {
        $this->exchangeRate = Product::getExchangeRate();
    }
    public function updatedTotalDiscount()
    {
        $this->total_discount = is_numeric($this->total_discount) ? (float)$this->total_discount : 0;
        if ($this->total_discount < 0) $this->total_discount = 0;
        if ($this->total_discount > $this->subtotal) $this->total_discount = $this->subtotal;
        $this->calculateTotals();
    }
    // Escucha en tiempo real cuando se escribe un monto inicial en el modal de crédito
    public function updatedInitialPayment()
    {
        $this->initial_payment = is_numeric($this->initial_payment) ? (float)$this->initial_payment : 0;
        if ($this->initial_payment < 0) $this->initial_payment = 0;
        if ($this->initial_payment > $this->total) $this->initial_payment = $this->total;

        $this->remaining_balance = $this->total - $this->initial_payment;
    }
    public function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['qty'];
        }

        if ($this->total_discount > $this->subtotal) {
            $this->total_discount = $this->subtotal;
        }

        $this->total = $this->subtotal - $this->total_discount;
        $this->remaining_balance = $this->total - (float)$this->initial_payment;
    }
    public function toggleCustomerModal()
    {
        $this->customerModal = !$this->customerModal;
        if (!$this->customerModal) {
            $this->reset(['quick_rif_ci', 'quick_name', 'quick_phone']);
            $this->resetValidation();
        }
    }
    public function saveQuickCustomer()
    {
        $this->validate([
            'quick_rif_ci' => 'required|string|max:50|unique:customers,rif_ci',
            'quick_name' => 'required|string|max:150',
            'quick_phone' => 'nullable|string|max:50',
        ], [
            'quick_rif_ci.required' => 'El RIF o Cédula es requerido.',
            'quick_rif_ci.unique' => 'Esta identificación ya existe.',
            'quick_name.required' => 'El nombre es requerido.',
        ]);

        $newCustomer = Customer::create([
            'rif_ci' => $this->quick_rif_ci,
            'name' => $this->quick_name,
            'phone' => $this->quick_phone ?: null,
            'type' => 'Individual'
        ]);

        // Lo asociamos inmediatamente a la venta en curso
        $this->selectedCustomerId = $newCustomer->id;
        $this->toggleCustomerModal();

        $this->dispatch('notify', title: 'Cliente Registrado', message: "{$newCustomer->name} asociado al POS con éxito.");
    }

    public function toggleCamera(): void
    {
        $this->cameraActive = !$this->cameraActive;
    }

    public function scanBarcode(string $scannedBarcode)
    {
        $product = Product::where('sku', $scannedBarcode)->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->dispatch('notify', title: 'Añadido', message: "{$product->name} agregado al carrito.");
        } else {
            $this->dispatch('notify', title: 'No encontrado', message: "El código de barras {$scannedBarcode} no coincide con ningún producto.");
        }
        $this->cameraActive = false;
    }

    public function addToCart(string $productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->quantity <= 0) {
            $this->dispatch('notify', title: 'Sin Stock', message: 'El producto seleccionado no cuenta con inventario disponible.');
            return;
        }

        // Si ya existe en la cesta, incrementamos cantidad
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] >= $product->quantity) {
                $this->dispatch('notify', title: 'Límite alcanzado', message: 'No puedes añadir más unidades de las disponibles en stock.');
                return;
            }
            $this->cart[$productId]['qty']++;
        } else {
            // Añadir nuevo ítem
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $product->selling_price,
                'qty' => 1
            ];
        }

        $this->calculateTotals();
    }

    public function updateQuantity(string $productId, $amount)
    {
        if (!isset($this->cart[$productId])) return;

        $product = Product::find($productId);
        $newQty = $this->cart[$productId]['qty'] + $amount;

        if ($newQty <= 0) {
            unset($this->cart[$productId]);
        } else {
            if ($newQty > $product->quantity) {
                $this->dispatch('notify', title: 'Límite alcanzado', message: "Solo quedan {$product->quantity} unidades de este artículo.");
                return;
            }
            $this->cart[$productId]['qty'] = $newQty;
        }

        $this->calculateTotals();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->calculateTotals();
    }

    public function selectPaymentMethod(string $method)
    {
        $this->payment_method = $method;
    }
    // Interceptor del botón Procesar Venta
    public function confirmSaleSubmission()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', title: 'Error', message: 'El carrito de compras se encuentra vacío.');
            return;
        }

        // Si es a Crédito, obligamos a asociar cliente y abrimos el modal intermedio de Inicial
        if ($this->payment_method === 'credit') {
            if (empty($this->selectedCustomerId)) {
                $this->dispatch('notify', title: 'Cliente Requerido', message: 'Las ventas a CRÉDITO deben asociarse obligatoriamente a un cliente.');
                return;
            }
            
            $this->initial_payment = '0.00';
            $this->remaining_balance = $this->total;
            $this->creditConfirmModal = true;
            return;
        }

        // Si es efectivo o tarjeta, procesa directo como antes
        $this->processSale();
    }
    public function processSale()
    {
        DB::transaction(function () {
            $invoiceNumber = 'NX-' . strtoupper(Str::random(8));
            $currentRate = Product::getExchangeRate();
            $initialPaid = (float)$this->initial_payment;

            // 1. Crear Cabecera Global de la Venta
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $this->selectedCustomerId ?: null,
                'created_by' => auth()->id() ?? DB::table('users')->first()?->id,
                'sale_date' => now(),
                'status' => 'completed',
                'subtotal' => $this->subtotal,
                'total_discount' => $this->total_discount,
                'total' => $this->total,
                'exchange_rate' => $currentRate,
                // Si hubo inicial a un crédito, registramos ese monto como dinero recibido en caja
                'cash_received' => $this->payment_method === 'credit' ? $initialPaid : $this->total,
                'change' => 0,
                'payment_method' => $this->payment_method,
                'notes' => $this->notes ?: null,
            ]);

            // 2. Registrar los ítems de la venta (Auditoría general)
            foreach ($this->cart as $item) {
                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);
            }

            // 3. Si fue a CRÉDITO, generar Cuenta por Cobrar (CxC) impactando el abono
            if ($this->payment_method === 'credit') {
                $ar = \App\Models\AccountsReceivable::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $this->selectedCustomerId,
                    'sale_date' => now(),
                    'due_date' => now()->addDays(15), // Plazo referencial
                    'total' => $this->total,
                    'paid_amount' => $initialPaid, // Registra el abono de la inicial en $
                    'balance' => $this->remaining_balance, // El saldo real deudor financiado en $
                ]);

                // Registrar ítems de las cuentas por cobrar
                foreach ($this->cart as $item) {
                    $product = Product::find($item['id']);
                    \App\Models\AccountsReceivableItem::create([
                        'accounts_receivable_id' => $ar->id,
                        'product_id' => $product->id,
                        'quantity' => $item['qty'],
                        'cost_price' => $product->purchase_price,
                        'unit_price' => $item['price'],
                        'discount' => 0,
                        'final_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['qty'],
                    ]);
                }
            }

            // 4. Descontar Inventario
            foreach ($this->cart as $item) {
                Product::where('id', $item['id'])->decrement('quantity', $item['qty']);
            }
        });

        // Limpieza total de estados
        $this->clearCart();
        $this->creditConfirmModal = false;
        $this->reset(['selectedCustomerId', 'payment_method', 'notes', 'total_discount', 'initial_payment', 'remaining_balance']);
        $this->dispatch('notify', title: '¡Venta Registrada!', message: 'La transacción y el esquema de crédito con inicial se guardaron con éxito.');
    }
    public function render()
    {
        $this->exchangeRate = Product::getExchangeRate();
        $products = Product::query()->where('quantity', '>', 0)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })->latest()->take(6)->get();

        return view('livewire.app.sale.index')->with([
            'catalog' => $products,
            'customers' => Customer::orderBy('name')->get()
        ]);
    }
}
