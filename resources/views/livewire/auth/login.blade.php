<div>
    <div>
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-primary text-on-primary rounded-xl mb-4 shadow-sm">
                <span class="material-symbols-outlined text-[28px]" data-icon="terminal">terminal</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Nexus Commercial</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-2">Professional Management Suite</p>
        </div>
        
        <!-- Login Card -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl login-card-elevation p-8 md:p-10 transition-all duration-300">
            <header class="mb-8">
                <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Welcome back</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Please enter your credentials to continue.</p>
            </header>

            <!-- Cambiado a wire:submit="authenticate" para coincidir con el método backend sugerido -->
            <form wire:submit="authenticate" class="space-y-6">
                
                <!-- Email Field -->
                <div class="space-y-1.5">
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="email">
                        Email Address
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline text-[20px]" data-icon="mail">mail</span>
                        </div>
                        <!-- Se añade wire:model y clases condicionales si hay un error -->
                        <input wire:model="email"
                            class="block w-full pl-11 pr-4 py-3 bg-surface-container-low border rounded-lg text-on-surface font-body-md placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all 
                            @error('email') border-error text-error @else border-outline-variant @enderror"
                            id="email" placeholder="admin@nexus.com" type="email" />
                    </div>
                    <!-- Error dinámico -->
                    @error('email')
                        <p class="font-body-sm text-body-sm text-error flex items-center gap-1 mt-1.5">
                            <span class="material-symbols-outlined text-[16px]" data-icon="error">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="password">
                            Password
                        </label>
                        <!-- Aquí colocarás la ruta de Fortify cuando hagas la vista de recuperación -->
                        <a class="font-label-caps text-label-caps text-secondary hover:text-secondary-container transition-colors" href="#">
                            Forgot password?
                        </a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline text-[20px]" data-icon="lock">lock</span>
                        </div>
                        <!-- Se añade wire:model y clases condicionales si hay un error -->
                        <input wire:model="password"
                            class="block w-full pl-11 pr-12 py-3 bg-surface-container-low border rounded-lg text-on-surface font-body-md placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all
                            @error('password') border-error text-error @else border-outline-variant @enderror"
                            id="password" placeholder="••••••••" type="password" />
                        
                        <!-- Si lo deseas, luego puedes meterle lógica AlpineJS a este botón para alternar el type="password/text" -->
                        <button class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-outline hover:text-on-surface-variant" type="button">
                            <span class="material-symbols-outlined text-[20px]" data-icon="visibility_off">visibility_off</span>
                        </button>
                    </div>
                    <!-- Error dinámico -->
                    @error('password')
                        <p class="font-body-sm text-body-sm text-error flex items-center gap-1 mt-1.5">
                            <span class="material-symbols-outlined text-[16px]" data-icon="error">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input wire:model="remember"
                        class="w-4 h-4 text-secondary border-outline-variant rounded focus:ring-secondary/30 transition-all cursor-pointer"
                        id="remember" type="checkbox" />
                    <label class="ml-2.5 font-body-sm text-body-sm text-on-surface-variant cursor-pointer select-none" for="remember">
                        Remember me for 30 days
                    </label>
                </div>

                <!-- Action Button con Estados de Carga -->
                <button wire:loading.attr="disabled"
                    class="w-full bg-primary text-on-primary py-3.5 px-6 rounded-lg font-headline-lg text-body-md hover:bg-neutral-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                    type="submit">
                    <span wire:loading.remove class="flex items-center gap-2">
                        Login to Dashboard
                        <span class="material-symbols-outlined text-[18px]" data-icon="arrow_forward">arrow_forward</span>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        Authenticating...
                        <!-- Puedes poner un pequeño spinner aquí si lo prefieres -->
                    </span>
                </button>
            </form>

            <!-- Social/Alternative Login -->
            <div class="mt-8 pt-8 border-t border-outline-variant">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-px bg-outline-variant flex-1"></div>
                    <span class="font-label-caps text-label-caps text-outline uppercase">Or enterprise login</span>
                    <div class="h-px bg-outline-variant flex-1"></div>
                </div>
                <button class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-outline-variant rounded-lg hover:bg-surface-container-low transition-colors font-body-md text-on-surface" type="button">
                    <div class="w-5 h-5 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]" data-icon="key">key</span>
                    </div>
                    Sign in with SSO
                </button>
            </div>
        </div>
    </div>
</div>