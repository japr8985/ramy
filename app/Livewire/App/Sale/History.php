<?php

namespace App\Livewire\App\Sale;

use App\Models\Product;
use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;
    // Filtros de Auditoría
    public $search = '';
    public $dateFrom;
    public $dateTo;
    public $paymentMethodFilter = '';

    // Estado del Modal de Detalle
    public bool $detailModal = false;
    public $selectedSale = null;

    public $exchangeRate = 1.0;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->exchangeRate = Product::getExchangeRate();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }
    public function updatingPaymentMethodFilter() { $this->resetPage(); }

    public function openDetailModal($saleId)
    {
        // Cargamos la venta con sus ítems, productos y el cliente asociado
        $this->selectedSale = Sale::with(['customer', 'items.product'])->findOrFail($saleId);
        $this->detailModal = true;
    }

    public function closeDetailModal()
    {
        $this->detailModal = false;
        $this->selectedSale = null;
    }

    public function render()
    {
        $sales = Sale::with('customer')
            ->when($this->search, function($query) {
                $query->where('invoice_number', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('customer', function($q) {
                        $q->where('name', 'ilike', '%' . $this->search . '%');
                    });
            })
            ->when($this->dateFrom, function($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->paymentMethodFilter, function($query) {
                $query->where('payment_method', $this->paymentMethodFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('livewire.app.sale.history')->with([
            'sales' => $sales
        ]);
    }
}
