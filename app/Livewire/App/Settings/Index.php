<?php

namespace App\Livewire\App\Settings;

use App\Models\Setting;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class Index extends Component
{
    // Propiedades mapeadas directamente al formulario
    public $business_name = '';
    public $tax_id = '';
    public $currency = 'USD';
    public $exchange_rate = '1.00';
    public $invoice_footer = '';

    // Estado para controlar el Toast de éxito de Livewire
    public $showToast = false;

    public function mount()
    {
        // Cargamos los valores actuales guardados en base de datos al inicializar
        $this->business_name = Setting::where('key', 'business_name')->value('value') ?? '';
        $this->tax_id = Setting::where('key', 'tax_id')->value('value') ?? '';
        $this->currency = Setting::where('key', 'currency')->value('value') ?? 'USD';
        $this->exchange_rate = Setting::where('key', 'bcv_rate')->value('value') ?? '1.00';
        $this->invoice_footer = Setting::where('key', 'invoice_footer')->value('value') ?? '';
    }

    public function save()
    {
        // Reglas de validación adaptadas a lo requerido por la UI
        $this->validate([
            'business_name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.01',
            'invoice_footer' => 'nullable|string|max:500',
        ], [
            'business_name.required' => 'El nombre de la empresa es requerido.',
            'tax_id.required' => 'El RIF / Identificación Fiscal es requerido.',
            'exchange_rate.required' => 'La tasa de cambio es requerida.',
        ]);

        // Guardar o actualizar masivamente usando updateOrCreate
        Setting::updateOrCreate(['key' => 'business_name'], ['value' => $this->business_name]);
        Setting::updateOrCreate(['key' => 'tax_id'], ['value' => $this->tax_id]);
        Setting::updateOrCreate(['key' => 'currency'], ['value' => $this->currency]);
        Setting::updateOrCreate(['key' => 'invoice_footer'], ['value' => $this->invoice_footer]);
        
        // Guardamos la tasa bcv
        Setting::updateOrCreate(['key' => 'bcv_rate'], ['value' => $this->exchange_rate]);

        // ¡Importante! Limpiar la caché de la tasa para que los productos adopten el cambio de inmediato
        Cache::forget('bcv_rate');

        // Disparar el Toast visual nativo de Livewire sin depender de scripts pesados
        $this->showToast = true;
    }

    public function discardChanges()
    {
        // Re-ejecutar el montado para limpiar campos modificados no guardados
        $this->mount();
        $this->showToast = false;
    }
    public function render()
    {
        return view('livewire.app.settings.index');
    }
}
