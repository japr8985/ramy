<template>
  <v-card rounded="xl" border elevation="0" class="units-manager-card overflow-hidden">
    <!-- Cabecera Principal -->
    <div class="pa-4 pa-sm-5 border-b d-flex align-center justify-space-between flex-wrap gap-2 bg-surface">
      <div class="d-flex align-center gap-3">
        <v-avatar color="primary" variant="tonal" size="42" class="rounded-lg">
          <v-icon size="22">mdi-scale-balance</v-icon>
        </v-avatar>
        <div>
          <div class="d-flex align-center gap-2">
            <span class="text-h6 font-weight-bold">Unidades de Medida</span>
            <v-chip size="x-small" color="primary" variant="flat" class="font-mono font-weight-bold">
              {{ units.length }}
            </v-chip>
          </div>
          <span class="text-caption text-medium-emphasis">
            Gestión de unidades para inventario y catálogo
          </span>
        </div>
      </div>
    </div>

    <v-card-text class="pa-4 pa-sm-5">
      <v-row>
        <!-- Columna 1: Lista de Unidades Existentes -->
        <v-col cols="12" md="7" class="d-flex flex-column">
          <div class="d-flex align-center justify-space-between mb-3">
            <span class="text-subtitle-2 font-weight-bold text-medium-emphasis text-uppercase tracking-wider">
              Unidades Registradas
            </span>
          </div>

          <!-- Contenedor con scroll si hay muchas unidades -->
          <div class="units-list-container border rounded-xl overflow-hidden flex-grow-1 bg-surface-light">
            <!-- Loading State -->
            <div v-if="isLoading" class="pa-3 d-flex flex-column gap-2">
              <v-skeleton-loader v-for="n in 4" :key="n" type="list-item-two-line" height="48" />
            </div>

            <!-- Empty State -->
            <div
              v-else-if="units.length === 0"
              class="d-flex flex-column align-center justify-center text-center pa-8 text-medium-emphasis h-100"
            >
              <v-avatar color="surface-variant" size="48" class="mb-2 opacity-60">
                <v-icon size="24">mdi-scale-unbalanced</v-icon>
              </v-avatar>
              <span class="text-body-2 font-weight-medium">No hay unidades registradas</span>
              <span class="text-caption text-disabled mt-1">Crea la primera unidad usando el formulario.</span>
            </div>

            <!-- Lista Nativa Limpia -->
            <v-list v-else lines="one" class="bg-transparent pa-1 overflow-y-auto" style="max-height: 380px;">
              <template v-for="(unit, index) in units" :key="unit.id || index">
                <v-list-item class="rounded-lg px-3 py-2 list-item-hover">
                  <!-- Nombre de la Unidad -->
                  <v-list-item-title class="font-weight-medium text-body-2">
                    {{ unit.name }}
                  </v-list-item-title>

                  <!-- Símbolo como Chip Cápsula -->
                  <template #append>
                    <v-chip
                      size="small"
                      variant="tonal"
                      color="primary"
                      class="font-mono font-weight-bold px-2 symbol-chip"
                    >
                      {{ unit.symbol }}
                    </v-chip>
                  </template>
                </v-list-item>

                <v-divider v-if="index < units.length - 1" class="border-opacity-15 my-1" />
              </template>
            </v-list>
          </div>
        </v-col>

        <!-- Columna 2: Formulario de Creación -->
        <v-col cols="12" md="5">
          <div class="form-card pa-4 pa-sm-5 border rounded-xl bg-surface-light h-100 d-flex flex-column justify-space-between">
            <div>
              <div class="d-flex align-center gap-2 mb-4">
                <v-icon size="18" color="primary">mdi-plus-circle-outline</v-icon>
                <span class="text-subtitle-2 font-weight-bold text-uppercase tracking-wider">
                  Nueva Unidad
                </span>
              </div>

              <v-form ref="formRef" @submit.prevent="handleSubmit">
                <!-- Nombre -->
                <div class="mb-3">
                  <v-text-field
                    v-model="unitForm.name"
                    label="Nombre de la unidad"
                    placeholder="Ej. Kilogramo, Unidad, Litro"
                    prepend-inner-icon="mdi-format-title"
                    variant="outlined"
                    density="comfortable"
                    rounded="lg"
                    :rules="[rules.required]"
                    :error-messages="fieldErrors.name"
                    hide-details="auto"
                    @update:model-value="clearFieldError('name')"
                  />
                </div>

                <!-- Símbolo -->
                <div class="mb-4">
                  <v-text-field
                    v-model="unitForm.symbol"
                    label="Símbolo / Abreviación"
                    placeholder="Ej. kg, und, lts, m"
                    prepend-inner-icon="mdi-pound"
                    variant="outlined"
                    density="comfortable"
                    rounded="lg"
                    :rules="[rules.required]"
                    :error-messages="fieldErrors.symbol"
                    hide-details="auto"
                    class="font-mono-input"
                    @update:model-value="clearFieldError('symbol')"
                  />
                  <div class="text-caption text-medium-emphasis mt-1 ps-1">
                    Este símbolo aparecerá en etiquetas, tickets y reportes.
                  </div>
                </div>

                <!-- Botón Crear -->
                <v-btn
                  color="primary"
                  type="submit"
                  size="large"
                  block
                  rounded="lg"
                  prepend-icon="mdi-plus"
                  :loading="isSubmitting"
                  class="font-weight-bold elevation-1 submit-btn"
                >
                  Registrar Unidad
                </v-btn>
              </v-form>
            </div>
          </div>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import catalogService from '@/api/catalog.api';
import { useNotificationStore } from '@/stores/notification';
import type { Unit } from '@/types/catalog';

const notificationStore = useNotificationStore();

// Estados
const units = ref<Unit[]>([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const formRef = ref<any>(null);

// Formulario y Errores
const initialFormState = (): Partial<Unit> => ({
  name: '',
  symbol: '',
});

const unitForm = ref<Partial<Unit>>(initialFormState());
const fieldErrors = ref<Record<string, string[]>>({});

// Reglas de validación frontend
const rules = {
  required: (v: string) => !!(v && v.trim()) || 'Este campo es obligatorio.',
};

function clearFieldError(field: string) {
  if (fieldErrors.value[field]) {
    delete fieldErrors.value[field];
  }
}

async function loadUnits() {
  isLoading.value = true;
  try {
    const data = await catalogService.getUnits();
    units.value = data;
  } catch (error: any) {
    notificationStore.warning('Error al sincronizar el listado de unidades.');
  } finally {
    isLoading.value = false;
  }
}

const handleSubmit = async () => {
  // 1. Validar reglas del frontend primero
  const { valid } = await formRef.value.validate();
  if (!valid) return;

  isSubmitting.value = true;
  fieldErrors.value = {};

  try {
    const payload = {
      name: unitForm.value.name?.trim(),
      symbol: unitForm.value.symbol?.trim(),
    } as Unit;

    await catalogService.createUnit(payload);
    
    notificationStore.success('Unidad creada exitosamente.');

    // Limpiar formulario y reiniciar validación
    unitForm.value = initialFormState();
    formRef.value?.resetValidation();

    // Recargar unidades
    await loadUnits();
  } catch (error: any) {
    // 2. Mapear errores del backend (422 Laravel / API)
    if (error?.response?.status === 422 && error?.response?.data?.errors) {
      fieldErrors.value = error.response.data.errors;
    } else {
      notificationStore.error(error?.response?.data?.message || 'Ocurrió un error al guardar la unidad.');
    }
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  loadUnits();
});
</script>

<style scoped>
.units-manager-card {
  background-color: rgb(var(--v-theme-surface));
  border-color: rgba(255, 255, 255, 0.08) !important;
}

.units-list-container {
  min-height: 220px;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

.form-card {
  border-color: rgba(255, 255, 255, 0.08) !important;
}

.list-item-hover {
  transition: background-color 0.12s ease;
}

.list-item-hover:hover {
  background-color: rgba(255, 255, 255, 0.03);
}

.symbol-chip {
  height: 24px !important;
  letter-spacing: 0.05em;
}

.submit-btn {
  height: 48px !important;
  letter-spacing: 0.02em;
}

.font-mono-input :deep(input) {
  font-family: monospace;
}

.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
.tracking-wider { letter-spacing: 0.05em; }
</style>