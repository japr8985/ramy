<template>
  <div class="settings-view w-100">
    <!-- Header de la sección -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Configuración del Sistema</h1>
        <p class="text-caption text-medium-emphasis">
          Gestione la identidad fiscal, parámetros monetarios y preferencias generales del negocio.
        </p>
      </div>

      <div class="d-flex align-center gap-2 mt-3 mt-sm-0">
        <v-btn
          variant="tonal"
          color="secondary"
          size="small"
          prepend-icon="mdi-restore"
          :disabled="isLoading || isSaving"
          @click="discardChanges"
        >
          Descartar
        </v-btn>

        <v-btn
          color="primary"
          size="small"
          prepend-icon="mdi-content-save-outline"
          :loading="isSaving"
          :disabled="isLoading"
          @click="saveSettings"
        >
          Guardar Cambios
        </v-btn>
      </div>
    </div>

    <!-- Skeleton Loader durante la carga inicial -->
    <v-row v-if="isLoading">
      <v-col cols="12" md="7">
        <v-skeleton-loader type="article, actions" class="border rounded-lg" />
      </v-col>
      <v-col cols="12" md="5">
        <v-skeleton-loader type="card" class="border rounded-lg" />
      </v-col>
    </v-row>

    <!-- Contenido del formulario -->
    <v-form v-else ref="formRef" @submit.prevent="saveSettings">
      <v-row>
        <!-- Columna Principal: Datos Fiscales y Facturación -->
        <v-col cols="12" md="7">
          <v-card elevation="0" border rounded="lg" class="pa-5 mb-5">
            <div class="d-flex align-center mb-4">
              <v-avatar color="primary" variant="tonal" size="36" rounded="lg" class="mr-3">
                <v-icon size="20">mdi-domain</v-icon>
              </v-avatar>
              <div>
                <h2 class="text-subtitle-1 font-weight-bold">Identidad Fiscal y Empresa</h2>
                <span class="text-caption text-medium-emphasis">Datos mostrados en encabezados y reportes</span>
              </div>
            </div>

            <v-divider class="mb-4" />

            <v-text-field
              v-model="form.business_name"
              label="Razón Social / Nombre Comercial"
              placeholder="Ej. Merey Technologies, C.A."
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-office-building-outline"
              :rules="[rules.required]"
              class="mb-3"
            />

            <v-text-field
              v-model="form.tax_id"
              label="RIF / Identificación Fiscal"
              placeholder="Ej. J-12345678-9"
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-card-account-details-outline"
              :rules="[rules.required]"
              class="mb-3"
            />

            <v-textarea
              v-model="form.invoice_footer"
              label="Pie de Factura / Condiciones Comerciales"
              placeholder="Notas al pie, condiciones de pago o cuentas bancarias..."
              variant="outlined"
              density="comfortable"
              rows="3"
              counter="500"
              prepend-inner-icon="mdi-text-box-outline"
              hint="Texto legal o de agradecimiento que se imprimirá al pie del comprobante."
              persistent-hint
            />
          </v-card>
        </v-col>

        <!-- Columna Lateral: Parámetros Monetarios y Tasa -->
        <v-col cols="12" md="5">
          <v-card elevation="0" border rounded="lg" class="pa-5 mb-5">
            <div class="d-flex align-center mb-4">
              <v-avatar color="secondary" variant="tonal" size="36" rounded="lg" class="mr-3">
                <v-icon size="20">mdi-currency-usd</v-icon>
              </v-avatar>
              <div>
                <h2 class="text-subtitle-1 font-weight-bold">Moneda & Tasas</h2>
                <span class="text-caption text-medium-emphasis">Base para conversión y facturación</span>
              </div>
            </div>

            <v-divider class="mb-4" />

            <v-select
              v-model="form.currency"
              :items="currencyOptions"
              label="Moneda Base"
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-cash-multiple"
              :rules="[rules.required]"
              class="mb-3"
            />

            <v-text-field
              v-model="form.exchange_rate"
              label="Tasa de Cambio Oficial (BCV / Ref)"
              type="number"
              step="0.01"
              min="0.01"
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-finance"
              prefix="Bs."
              :rules="[rules.required, rules.positiveNumber]"
              class="mb-2"
            />

            <v-alert
              type="info"
              variant="tonal"
              density="compact"
              rounded="lg"
              class="text-caption mt-3"
            >
              Al modificar la tasa, se invalida la caché en el servidor para que los precios se reflejen de inmediato.
            </v-alert>
          </v-card>

          <!-- Tarjeta de Estado del Módulo -->
          <v-card elevation="0" border rounded="lg" class="pa-4 bg-surface-light">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <v-icon size="18" color="success" class="mr-2">mdi-check-circle-outline</v-icon>
                <span class="text-caption font-weight-medium">Módulo Persistente</span>
              </div>
              <v-chip size="x-small" variant="flat" color="surface-variant" class="font-mono">
                Key-Value Store
              </v-chip>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import settingsService from '@/api/settings.api';
import { useNotificationStore } from '@/stores/notification';
import type { SettingsMap } from '@/types/settings';

const notificationStore = useNotificationStore();

const formRef = ref<HTMLFormElement | null>(null);
const isLoading = ref(true);
const isSaving = ref(false);

// Copia original para restaurar si el usuario descarta cambios
let originalSettings: SettingsMap = {};

// Modelo reactivo para el formulario
const form = reactive<SettingsMap>({
  business_name: '',
  tax_id: '',
  currency: 'USD',
  exchange_rate: '1.00',
  invoice_footer: '',
});

const currencyOptions = [
  { title: 'Dólar Estadounidense (USD)', value: 'USD' },
  { title: 'Bolívares (VES)', value: 'VES' },
  { title: 'Euros (EUR)', value: 'EUR' },
];

const rules = {
  required: (v: unknown) => !!v || 'Este campo es obligatorio',
  positiveNumber: (v: string | number) => {
    const num = Number(v);
    return (!isNaN(num) && num > 0) || 'Debe ser un número mayor a 0';
  },
};

/**
 * Carga las configuraciones desde la API
 */
async function loadSettings() {
  isLoading.value = true;
  try {
    const data = await settingsService.getSettings();
    originalSettings = { ...data };
    Object.assign(form, data);
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al obtener las configuraciones del sistema.'
    );
  } finally {
    isLoading.value = false;
  }
}

/**
 * Guarda los cambios enviando el mapa dinámico
 */
async function saveSettings() {
  if (!formRef.value) return;

  const { valid } = await formRef.value.validate();
  if (!valid) {
    notificationStore.warning('Por favor verifique los campos requeridos.');
    return;
  }

  isSaving.value = true;
  try {
    const updatedData = await settingsService.updateSettings(form);
    originalSettings = { ...updatedData };
    Object.assign(form, updatedData);

    notificationStore.success('Configuraciones guardadas y tasa actualizada correctamente.');
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al actualizar las configuraciones.',
    );
  } finally {
    isSaving.value = false;
  }
}

/**
 * Descarta los cambios y restaura los valores obtenidos inicialmente
 */
function discardChanges() {
  Object.assign(form, originalSettings);
  notificationStore.info('Cambios descartados.');
}

onMounted(() => {
  loadSettings();
});
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
.font-mono {
  font-family: monospace;
}
</style>