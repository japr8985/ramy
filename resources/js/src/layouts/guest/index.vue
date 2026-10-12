<template>
    <v-container fluid class="pa-0 fill-height login-wrapper">
        <v-row no-gutters class="fill-height">
            <!-- Columna Izquierda: Artwork / Wallpaper SDDM -->
            <v-col cols="12" md="7" lg="8" class="wallpaper-pane d-none d-md-flex align-end pa-10">
                <div class="glass-badge pa-4">
                <div class="text-h6 font-weight-bold text-white">System Gateway</div>
                <div class="text-caption text-grey-lighten-2 font-mono">Workspace Authentication Node</div>
                </div>
            </v-col>
            <!-- Columna Derecha: Contenido -->
             <v-col cols="12" md="5" lg="4" class="login-pane d-flex align-center justify-center pa-6">
                <slot />
             </v-col>
        </v-row>
    </v-container>
</template>
<style>
.login-wrapper {
  background-color: #0f111a;
  min-height: 100vh;
}

.wallpaper-pane {
  background: 
    linear-gradient(to right, rgba(15, 17, 26, 0.2), rgba(15, 17, 26, 0.85)),
    url('/images/bg-auth.jpeg') center/cover no-repeat;
  position: relative;
}

.glass-badge {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
}

.login-pane {
  background-color: #12141f;
  border-left: 1px solid rgba(255, 255, 255, 0.05);
}

.login-card {
  background: transparent !important;
}

.font-mono {
  font-family: monospace;
}

/* Card Principal con efecto cristal translúcido oscuro */
.auth-card {
  background: rgba(18, 20, 29, 0.6) !important;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.05) !important;
  border-radius: 16px !important;
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
}

.auth-card:hover {
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4) !important;
}

/* Efecto Glow en el Icono */
.brand-glow-container {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  position: relative;
}

.brand-avatar-glow {
  background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.15), rgba(var(--v-theme-primary), 0.05));
  border: 1px solid rgba(var(--v-theme-primary), 0.3);
  border-radius: 50%;
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 25px rgba(var(--v-theme-primary), 0.2);
  position: relative;
}

.brand-avatar-glow::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(var(--v-theme-primary), 0.4) 0%, transparent 70%);
  z-index: 1;
  pointer-events: none;
}

.lock-icon {
  color: rgb(var(--v-theme-primary)) !important;
  z-index: 2;
  text-shadow: 0 0 8px rgba(var(--v-theme-primary), 0.5);
}

/* Tipografía */
.auth-title {
  color: #ffffff;
  letter-spacing: -0.5px !important;
}

.auth-subtitle {
  color: #a0aec0 !important;
}

/* Labels de los inputs */
.input-label {
  letter-spacing: 0.8px;
  font-size: 0.7rem !important;
  opacity: 0.85;
}

/* Enlaces */
.forgot-link {
  font-size: 0.78rem !important;
  text-decoration: none !important;
  transition: color 0.2s ease, text-shadow 0.2s ease;
}

.forgot-link:hover {
  color: lighten(rgb(var(--v-theme-primary)), 10%) !important;
  text-shadow: 0 0 8px rgba(var(--v-theme-primary), 0.3);
}

/* Personalización de inputs Vuetify */
:deep(.custom-field .v-field) {
  border-radius: 10px !important;
  background-color: rgba(255, 255, 255, 0.02) !important;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

:deep(.custom-field .v-field:hover) {
  background-color: rgba(255, 255, 255, 0.04) !important;
}

:deep(.custom-field .v-field--focused) {
  background-color: rgba(255, 255, 255, 0.05) !important;
  box-shadow: 0 0 0 3px rgba(var(--v-theme-primary), 0.1) !important;
}

:deep(.custom-field .v-field__input) {
  font-size: 0.95rem !important;
  color: #ffffff !important;
}

:deep(.custom-field .v-field__prepend-inner .v-icon),
:deep(.custom-field .v-field__append-inner .v-icon) {
  opacity: 0.6;
  transition: opacity 0.2s ease, color 0.2s ease;
}

:deep(.custom-field .v-field--focused .v-field__prepend-inner .v-icon),
:deep(.custom-field .v-field--focused .v-field__append-inner .v-icon) {
  opacity: 1;
  color: rgb(var(--v-theme-primary)) !important;
}

/* Checkbox */
:deep(.custom-checkbox .v-label) {
  font-size: 0.85rem !important;
  color: #a0aec0 !important;
  opacity: 1 !important;
  user-select: none;
}

/* Botón de Enviar con degradado e iluminaciones */
.submit-btn {
  background: linear-gradient(135deg, rgb(var(--v-theme-primary)), darken(rgb(var(--v-theme-primary)), 10%)) !important;
  letter-spacing: 0.3px;
  overflow: hidden;
  position: relative;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.submit-btn::before {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
  transform: translateX(-100%);
  transition: none;
}

.submit-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(var(--v-theme-primary), 0.3) !important;
}

.submit-btn:hover::before {
  transform: translateX(100%);
  transition: transform 0.6s ease-in-out;
}

.submit-btn:active {
  transform: translateY(1px);
}

.error-alert {
  border: 1px solid rgba(244, 67, 54, 0.2) !important;
}
</style>