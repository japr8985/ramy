<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.auth')]
class Login extends Component
{
    #[Rule('required|email|string')]
    public string $email = '';

    #[Rule('required|string')]
    public string $password = '';

    public bool $remember = false;
    public function authenticate()
    {
        // 1. Ejecuta las reglas de validación automáticas de los atributos
        $this->validate();

        // 2. Intenta hacer login usando la configuración tradicional de Laravel Auth
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // Opcional: Si quieres registrar los intentos fallidos con Fortify/RateLimiter puedes hacerlo aquí.
            // Por ahora, lanzamos la excepción estándar de validación para el campo email.
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // 3. Regenera la sesión para evitar ataques de fijación de sesión (Session Fixation)
        session()->regenerate();

        // 4. Redirige al dashboard del SaaS
        return redirect()->intended(route('dashboard'));
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
