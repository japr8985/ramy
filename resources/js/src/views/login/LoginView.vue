<template>
  <v-card 
    width="100%" 
    max-width="440" 
    elevation="0" 
    class="auth-card pa-8 border-1"
  >
    <!-- Cabecera con Branding e Icono -->
    <div class="text-center mb-8">
      <div class="brand-glow-container mb-4">
        <div class="brand-avatar-glow">
          <v-icon size="30" class="lock-icon">mdi-shield-key-outline</v-icon>
        </div>
      </div>
      <h1 class="auth-title text-h5 font-weight-black tracking-tight mb-1">
        Bienvenido de nuevo
      </h1>
      <p class="auth-subtitle text-body-2 text-grey-darken-1">
        Ingresa tus credenciales para acceder a <span class="text-primary font-weight-bold">Merey</span>
      </p>
    </div>

    <!-- Mensaje de Error con Diseño de Alerta Suave -->
    <v-fade-transition>
      <v-alert
        v-if="errorMessage"
        type="error"
        variant="tonal"
        density="comfortable"
        class="mb-6 rounded-lg error-alert"
        closable
        @click:close="errorMessage = ''"
      >
        <template v-slot:prepend>
          <v-icon class="mr-1">mdi-alert-circle-outline</v-icon>
        </template>
        <span class="text-caption font-weight-medium">{{ errorMessage }}</span>
      </v-alert>
    </v-fade-transition>

    <!-- Formulario de Acceso -->
    <v-form @submit.prevent="handleLogin" :disabled="loading">
      
      <!-- Input de Email -->
      <div class="input-group mb-4">
        <label class="input-label text-caption font-weight-bold text-grey-lighten-1 d-block mb-1">
          CORREO ELECTRÓNICO
        </label>
        <v-text-field
          v-model="loginForm.email"
          placeholder="nombre@ejemplo.com"
          prepend-inner-icon="mdi-email-outline"
          variant="outlined"
          density="comfortable"
          type="email"
          autocomplete="email"
          color="primary"
          class="custom-field"
          hide-details="auto"
          required
        />
      </div>

      <!-- Input de Contraseña -->
      <div class="input-group mb-4">
        <div class="d-flex justify-space-between align-center mb-1">
          <label class="input-label text-caption font-weight-bold text-grey-lighten-1">
            CONTRASEÑA
          </label>
          <!-- Enlace de recuperación estilizado -->
          <router-link 
            to="/forgot-password" 
            class="forgot-link text-caption text-primary font-weight-medium decoration-none"
          >
            ¿La olvidaste?
          </router-link>
        </div>
        <v-text-field
          v-model="loginForm.password"
          placeholder="••••••••"
          prepend-inner-icon="mdi-key-outline"
          :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
          :type="showPassword ? 'text' : 'password'"
          variant="outlined"
          density="comfortable"
          autocomplete="current-password"
          color="primary"
          class="custom-field"
          hide-details="auto"
          @click:append-inner="showPassword = !showPassword"
          required
        />
      </div>

      <!-- Recuerdame Checkbox -->
      <div class="d-flex align-center justify-between mb-6 mt-2">
        <v-checkbox
          v-model="loginForm.remember"
          label="Mantener sesión iniciada"
          density="compact"
          hide-details
          color="primary"
          class="custom-checkbox"
        />
      </div>

      <!-- Botón de Envío -->
      <v-btn
        type="submit"
        color="primary"
        size="large"
        block
        :loading="loading"
        class="submit-btn text-none font-weight-bold rounded-lg elevation-4"
        height="48"
      >
        <span class="btn-text">Acceder al sistema</span>
        <template v-slot:loader>
          <v-progress-circular indeterminate size="22" width="2" color="white" />
        </template>
      </v-btn>
    </v-form>

    <!-- Pie de Tarjeta / Nota opcional -->
    <div class="text-center mt-6">
      <span class="text-caption text-grey-darken-1">
        ¿Problemas para entrar? Contacta a soporte
      </span>
    </div>
  </v-card>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();

const { login } = authStore
const { loading, loginForm } = storeToRefs(authStore);

const router = useRouter();

const showPassword = ref(false);
const errorMessage = ref('');


async function handleLogin() {
  errorMessage.value = '';

  try {
    await login(loginForm.value)
    router.push({ name: 'home' });
  } catch (error: any) {
    errorMessage.value = error.message;
  } finally {
    // isLoading.value = false;
  }
}
</script>

