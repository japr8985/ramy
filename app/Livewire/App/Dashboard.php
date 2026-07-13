<?php

namespace App\Livewire\App;

use App\Models\AccountsReceivable;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $exchangeRate = 1.0;

    // Filtros de Rango Global
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->exchangeRate = Product::getExchangeRate();

        // Por defecto inicializamos el mes en curso para no sobrecargar el POS al entrar
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    // Resetear paginaciones o estados si cambian las fechas
    public function updatedDateFrom() {}
    public function updatedDateTo() {}

    public function render()
    {
        $start = Carbon::parse($this->dateFrom)->startOfDay();
        $end = Carbon::parse($this->dateTo)->endOfDay();
        $diffInDays = $start->diffInDays($end);

        // 1. KPIs acoplados al Filtro de Fechas Seleccionado
        $filteredSales = Sale::whereBetween('created_at', [$start, $end])->sum('total');
        $pendingReceivables = AccountsReceivable::sum('balance'); // Estático: Deuda viva actual
        $inventoryValue = Product::select(DB::raw('SUM(quantity * purchase_price) as total_value'))->first()->total_value ?? 0;
        $lowStockCount = Product::whereRaw('quantity <= min_stock')->count();

        // 2. Orquestación del Gráfico Adaptativo (Día vs Mes) para Postgres
        $chartBars = collect();

        if ($diffInDays <= 15) {
            // Agrupación por Días
            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $chartBars->put($date->format('Y-m-d'), [
                    'label' => ucfirst($date->isoFormat('DD MMM')),
                    'is_current' => $date->isToday(),
                    'total' => 0
                ]);
            }

            $salesQuery = Sale::whereBetween('created_at', [$start, $end])
                ->select(DB::raw('DATE(created_at) as group_key'), DB::raw('SUM(total) as daily_total'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();
        } else {
            // Agrupación por Meses (Evita colapsar el Bento Grid si el rango es muy amplio)
            $period = CarbonPeriod::create($start, '1 month', $end);
            foreach ($period as $date) {
                $chartBars->put($date->format('Y-m'), [
                    'label' => ucfirst($date->isoFormat('MMMM Y')),
                    'is_current' => $date->isCurrentMonth(),
                    'total' => 0
                ]);
            }

            $salesQuery = Sale::whereBetween('created_at', [$start, $end])
                ->select(DB::raw("TO_CHAR(created_at, 'YYYY-MM') as group_key"), DB::raw('SUM(total) as daily_total'))
                ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
                ->get();
        }

        // Inyectar montos reales en el esqueleto del periodo
        foreach ($salesQuery as $sale) {
            if ($chartBars->has($sale->group_key)) {
                $chartBars->transform(function ($item, $key) use ($sale) {
                    if ($key === $sale->group_key) {
                        $item['total'] = (float) $sale->daily_total;
                    }
                    return $item;
                });
            }
        }

        // Calcular alturas relativas de barras CSS
        $maxVolume = collect($chartBars->pluck('total'))->max() ?: 1;
        $formattedChartBars = $chartBars->map(function ($bar) use ($maxVolume) {
            $bar['height_percentage'] = min(100, max(6, round(($bar['total'] / $maxVolume) * 100)));
            return $bar;
        });

        // 3. Métricas Complementarias del Periodo
        $topCustomerRow = Sale::whereBetween('sales.created_at', [$start, $end])
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('SUM(sales.total) as total_spent'))
            ->groupBy('customers.name')
            ->orderBy('total_spent', 'desc')
            ->first();

        $topCustomer = $topCustomerRow ? $topCustomerRow->name : 'Consumidor Final';

        $criticalProducts = Product::with('category')->whereRaw('quantity <= min_stock')->orderBy('quantity', 'asc')->take(5)->get();
        $recentSales = Sale::with('customer')->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->take(5)->get();

        // Cuentas de ganancia corregidas con tu campo 'price' y el 'subtotal' directo
        $periodEarnings = Sale::whereBetween('sales.created_at', [$start, $end])
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                DB::raw('SUM(sale_items.subtotal) as total_revenue'), // Usamos directamente tu columna subtotal
                DB::raw('SUM(sale_items.quantity * products.purchase_price) as total_cost')
            )
            ->first();

        $totalRevenue = (float) ($periodEarnings->total_revenue ?? 0);
        $totalCost = (float) ($periodEarnings->total_cost ?? 0);
        $netProfit = $totalRevenue - $totalCost;

        // Evitar división por cero si no hay ventas en el rango
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        return view('livewire.app.dashboard')->with([
            'kpis' => [
                'filtered_sales' => $filteredSales,
                'pending_receivables' => $pendingReceivables,
                'inventory_value' => $inventoryValue,
                'low_stock_count' => $lowStockCount,
            ],
            'chartBars' => $formattedChartBars,
            'topCustomer' => $topCustomer,
            'criticalProducts' => $criticalProducts,
            'recentSales' => $recentSales,
            'isFiltered' => $diffInDays > 0,
            // Pasamos las nuevas métricas a la vista
            'profit' => [
                'net_amount' => $netProfit,
                'margin_percentage' => $profitMargin
            ]
        ]);
    }
}
