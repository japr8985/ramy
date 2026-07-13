<?php
namespace App\Livewire\App\AccountsReceivable;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AccountsReceivable;
use App\Models\ReceivablePayment;
use App\Models\Product;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public bool $modal = false;
    public bool $historyModal = false; 
    public $exchangeRate = 1.0;

    // Propiedades para procesar el abono
    public $selectedInvoiceId;
    public $customer_name;
    public $current_invoice_balance = 0; 
    
    // Formulario de Abono Reactivo vinculados a la vista
    public $payment_date;
    
    public $input_currency = 'USD'; 
    public $amount_usd = 0;        
    public $amount_ves = 0;        
    public $payment_method = 'Bank Transfer';
    public $notes = '';

    public array $paymentHistory = [];

    public function updatingSearch() { $this->resetPage(); }

    public function openPaymentModal($id)
    {
        $this->resetValidation();
        $invoice = AccountsReceivable::with('customer')->findOrFail($id);
        
        $this->selectedInvoiceId = $id;
        $this->customer_name = $invoice->customer->name;
        $this->current_invoice_balance = (float) $invoice->balance;
        
        $this->exchangeRate = Product::getExchangeRate();
        $this->input_currency = 'USD';
        
        // Inicializar los montos sugeridos directamente aquí al abrir el modal
        $this->amount_usd = $this->current_invoice_balance;
        $this->amount_ves = round($this->amount_usd * $this->exchangeRate, 2);
        
        $this->payment_date = now()->format('Y-m-d');
        $this->modal = true;
    }

    public function closePaymentModal()
    {
        $this->modal = false;
        $this->reset(['selectedInvoiceId', 'customer_name', 'current_invoice_balance', 'amount_usd', 'amount_ves', 'notes']);
    }

    public function openHistoryModal($id)
    {
        $invoice = AccountsReceivable::with(['customer', 'payments'])->findOrFail($id);
        $this->customer_name = $invoice->customer->name;
        $this->paymentHistory = $invoice->payments->toArray();
        $this->historyModal = true;
    }

    public function closeHistoryModal()
    {
        $this->historyModal = false;
        $this->reset(['paymentHistory', 'customer_name']);
    }

    public function postPayment()
    {
        // Convertimos el valor entrante a flotante para validación segura
        $this->amount_usd = (float) $this->amount_usd;

        $this->validate([
            'amount_usd' => 'required|numeric|min:0.01|max:' . $this->current_invoice_balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:255'
        ], [
            'amount_usd.required' => 'El monto a abonar es obligatorio.',
            'amount_usd.max' => 'El abono no puede exceder el saldo deudor de la cuenta.',
        ]);

        \DB::transaction(function () {
            $invoice = AccountsReceivable::findOrFail($this->selectedInvoiceId);
            $currentRate = Product::getExchangeRate();

            ReceivablePayment::create([
                'accounts_receivable_id' => $invoice->id,
                'payment_date' => $this->payment_date,
                'amount_usd' => $this->amount_usd,
                'exchange_rate' => $currentRate,
                'payment_method' => $this->payment_method,
                'notes' => $this->notes ?: null,
            ]);

            $invoice->increment('paid_amount', $this->amount_usd);
            $invoice->decrement('balance', $this->amount_usd);
        });

        $this->closePaymentModal();
        $this->dispatch('notify', title: '¡Abono Procesado!', message: 'El balance financiero del cliente fue actualizado con éxito.');
    }

    public function render()
    {
        $this->exchangeRate = Product::getExchangeRate();

        $invoices = AccountsReceivable::with(['customer', 'sale'])
            ->where('balance', '>', 0)
            ->when($this->search, function($query) {
                $query->whereHas('customer', function($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%');
                })->orWhereHas('sale', function($q) {
                    $q->where('invoice_number', 'ilike', '%' . $this->search . '%');
                });
            })
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        $stats = [
            'total_receivables' => AccountsReceivable::sum('balance'),
            'overdue_amount' => AccountsReceivable::where('due_date', '<', now())->sum('balance'),
            'collected_mtd' => ReceivablePayment::whereMonth('payment_date', now()->month)->sum('amount_usd')
        ];

        return view('livewire.app.accounts_receivable.index')->with([
            'invoices' => $invoices,
            'stats' => $stats
        ]);
    }
}
