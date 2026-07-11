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
    public bool $historyModal = false; // Control de la vista del historial
    public $exchangeRate = 1.0;

    // Propiedades para procesar el abono
    public $selectedInvoiceId;
    public $customer_name;
    public $current_invoice_balance = 0; // Guardado en $
    
    // Formulario de Abono Reactivo
    public $payment_date;
    public $input_currency = 'USD'; // Conmutador: USD o VES
    public $amount_usd = '';        // Lo que se descuenta contablemente de la deuda
    public $amount_ves = '';        // Lo que se digita si el cliente paga en Bs.
    public $payment_method = 'Bank Transfer';
    public $notes = '';


    // Colección para la subvista del historial
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
        
        // Inicializar los montos con el pago total sugerido
        $this->amount_usd = $this->current_invoice_balance;
        $this->amount_ves = round($this->amount_usd * $this->exchangeRate, 2);
        
        $this->payment_date = now()->format('Y-m-d');
        $this->modal = true;
    }

    // Escucha en tiempo real si el operador escribe en el campo de Dólares ($)
    public function updatedAmountUsd()
    {
        $this->amount_usd = is_numeric($this->amount_usd) ? (float)$this->amount_usd : 0;
        
        // Bloquear topes lógicos
        if ($this->amount_usd < 0) $this->amount_usd = 0;
        if ($this->amount_usd > $this->current_invoice_balance) $this->amount_usd = $this->current_invoice_balance;

        // Calcular contraparte en Bs.
        $this->amount_ves = round($this->amount_usd * $this->exchangeRate, 2);
    }

    // Escucha en tiempo real si el operador escribe en el campo de Bolívares (Bs.)
    public function updatedAmountVes()
    {
        $this->amount_ves = is_numeric($this->amount_ves) ? (float)$this->amount_ves : 0;
        $maxVes = $this->current_invoice_balance * $this->exchangeRate;

        // Bloquear topes lógicos
        if ($this->amount_ves < 0) $this->amount_ves = 0;
        if ($this->amount_ves > $maxVes) $this->amount_ves = $maxVes;

        // Calcular conversión inversa hacia Dólares ($) de forma exacta
        $this->amount_usd = $this->exchangeRate > 0 ? round($this->amount_ves / $this->exchangeRate, 4) : 0;
    }

    // Si el operador cambia el botón de moneda, resincronizamos
    public function setCurrency($currency)
    {
        $this->input_currency = $currency;
    }

    public function closePaymentModal()
    {
        $this->modal = false;
        $this->reset(['selectedInvoiceId', 'customer_name', 'current_invoice_balance', 'amount_usd', 'amount_ves', 'notes']);
    }

    // NUEVO: Cargar cronología histórica de pagos recibidos
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

            // Guardar el abono mapeando la conversión limpia en dólares ($)
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
