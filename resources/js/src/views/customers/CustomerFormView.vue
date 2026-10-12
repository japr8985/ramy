<template>
  <div class="customer-form-view w-100">
    <!-- Encabezado de la Página -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div class="d-flex align-center gap-3">
        <v-btn
          icon="mdi-arrow-left"
          variant="text"
          density="comfortable"
          :to="{ name: 'customers.index' }"
          title="Regresar al directorio"
        />
        <div>
          <h1 class="text-h5 font-weight-bold">
            {{ isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}
          </h1>
          <p class="text-caption text-medium-emphasis">
            {{ isEditing ? 'Modifique la información fiscal o de contacto del cliente.' : 'Complete la ficha para registrar un nuevo cliente en el sistema.' }}
          </p>
        </div>
      </div>

      <div class="d-flex align-center gap-2 mt-3 mt-sm-0">
        <v-btn
          variant="tonal"
          color="secondary"
          size="small"
          :to="{ name: 'customers.index' }"
          :disabled="isSaving"
        >
          Cancelar
        </v-btn>

        <v-btn
          color="primary"
          size="small"
          prepend-icon="mdi-content-save-outline"
          :loading="isSaving"
          :disabled="isLoading"
          @click="submit"
        >
          {{ isEditing ? 'Actualizar Cliente' : 'Guardar Cliente' }}
        </v-btn>
      </div>
    </div>

    <!-- Skeleton durante carga de datos de edición -->
    <v-card v-if="isLoading" elevation="0" border rounded="lg" class="pa-6">
      <v-skeleton-loader type="article, actions" />
    </v-card>

    <!-- Formulario Principal -->
    <v-form v-else ref="formRef" @submit.prevent="submit">
      <v-row>
        <!-- Columna Principal: Datos Fiscales e Identificación -->
        <v-col cols="12" md="8">
          <v-card elevation="0" border rounded="lg" class="pa-5 mb-5">
            <h2 class="text-subtitle-1 font-weight-bold mb-1">Identificación Fiscal</h2>
            <span class="text-caption text-medium-emphasis d-block mb-4">
              Datos requeridos para comprobantes de pago y facturación.
            </span>

            <v-row dense>
              <v-col cols="12" sm="5">
                <v-text-field
                  v-model="form.rif_ci"
                  label="Cédula / RIF *"
                  placeholder="Ej. V-12345678 o J-123456789"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-card-account-details-outline"
                  :rules="[rules.required, rules.maxLength(50)]"
                  class="mb-2"
                />
              </v-col>

              <v-col cols="12" sm="7">
                <v-radio-group
                  v-model="form.type"
                  inline
                  density="compact"
                  hide-details
                  class="pt-1"
                >
                  <v-radio label="Natural / Persona" value="Individual" color="primary" />
                  <v-radio label="Jurídico / Empresa" value="Corporate" color="primary" />
                </v-radio-group>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="form.name"
                  label="Nombre o Razón Social *"
                  placeholder="Ej. Distribuidora Central C.A. / Juan Pérez"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-account-outline"
                  :rules="[rules.required, rules.maxLength(150)]"
                  class="mb-2"
                />
              </v-col>
            </v-row>
          </v-card>

          <!-- Bloque de Contacto y Dirección -->
          <v-card elevation="0" border rounded="lg" class="pa-5">
            <h2 class="text-subtitle-1 font-weight-bold mb-1">Contacto y Ubicación</h2>
            <span class="text-caption text-medium-emphasis d-block mb-4">
              Canales de comunicación directa y dirección de despacho/fiscal.
            </span>

            <v-row dense>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.phone"
                  label="Teléfono de Contacto"
                  placeholder="Ej. 0414-1234567"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-phone-outline"
                  :rules="[rules.maxLength(50)]"
                  class="mb-2"
                />
              </v-col>

              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.email"
                  label="Correo Electrónico"
                  placeholder="cliente@ejemplo.com"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-email-outline"
                  :rules="[rules.email, rules.maxLength(150)]"
                  class="mb-2"
                />
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.address"
                  label="Dirección Fiscal / Despacho"
                  placeholder="Calle, avenida, edificio o referencias de entrega..."
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-map-marker-outline"
                  rows="3"
                  counter="500"
                  :rules="[rules.maxLength(500)]"
                />
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <!-- Columna Lateral: Notas de Gestión y Resumen -->
        <v-col cols="12" md="4">
          <v-card elevation="0" border rounded="lg" class="pa-5 mb-5">
            <h2 class="text-subtitle-1 font-weight-bold mb-1">Notas Comerciales</h2>
            <span class="text-caption text-medium-emphasis d-block mb-3">
              Información interna visible sólo para el personal de ventas y caja.
            </span>

            <v-textarea
              v-model="form.notes"
              label="Notas u Observaciones"
              placeholder="Preferencias de pago, días de recepción, etc."
              variant="outlined"
              density="comfortable"
              rows="4"
              counter="500"
              :rules="[rules.maxLength(500)]"
            />
          </v-card>

          <!-- Resumen de Sistema si es Edición -->
          <v-card v-if="isEditing" elevation="0" border rounded="lg" class="pa-4 bg-surface-light">
            <span class="text-caption font-weight-bold d-block mb-1">Información de Auditoría</span>
            <div class="text-caption text-medium-emphasis">
              <div><strong>ID:</strong> <span class="font-mono">{{ customerId }}</span></div>
              <div v-if="salesCount !== null" class="mt-1">
                <strong>Ventas registradas:</strong> {{ salesCount }}
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import partiesService from '@/services/partiesService';
import { useNotificationStore } from '@/stores/notification';
import type { CreateCustomerPayload, CustomerType } from '@/types/parties';

const route = useRoute();
const router = useRouter();
const notificationStore = useNotificationStore();

const customerId = computed(() => route.params.id as string | undefined);
const isEditing = computed(() => !!customerId.value);

const isLoading = ref(false);
const isSaving = ref(false);
const salesCount = ref<number | null>(null);
const formRef = ref<HTMLFormElement | null>(null);

const form = reactive<CreateCustomerPayload>({
  rif_ci: '',
  name: '',
  type: 'Individual',
  email: '',
  phone: '',
  address: '',
  notes: '',
});

const rules = {
  required: (v: unknown) => !!v || 'Este campo es obligatorio.',
  maxLength: (max: number) => (v: string | null | undefined) =>
    !v || v.length <= max || `Máximo ${max} caracteres permitidos.`,
  email: (v: string | null | undefined) =>
    !v || /.+@.+\..+/.test(v) || 'Ingrese un formato de correo electrónico válido.',
};

async function loadCustomer(id: string) {
  isLoading.value = true;
  try {
    const customer = await partiesService.getCustomerById(id);
    form.rif_ci = customer.rif_ci;
    form.name = customer.name;
    form.type = customer.type as CustomerType;
    form.email = customer.email || '';
    form.phone = customer.phone || '';
    form.address = customer.address || '';
    form.notes = customer.notes || '';
    salesCount.value = customer.sales_count ?? null;
  } catch (error: any) {
    notificationStore.show('Cliente no encontrado.', 'error');
    router.push({ name: 'customers.index' });
  } finally {
    isLoading.value = false;
  }
}

async function submit() {
  if (!formRef.value) return;

  const { valid } = await formRef.value.validate();
  if (!valid) {
    notificationStore.show('Por favor verifique los campos con error.', 'warning');
    return;
  }

  isSaving.value = true;
  try {
    const payload: CreateCustomerPayload = {
      rif_ci: form.rif_ci.trim(),
      name: form.name.trim(),
      type: form.type,
      email: form.email?.trim() || null,
      phone: form.phone?.trim() || null,
      address: form.address?.trim() || null,
      notes: form.notes?.trim() || null,
    };

    if (isEditing.value && customerId.value) {
      await partiesService.updateCustomer(customerId.value, payload);
      notificationStore.show('Cliente actualizado correctamente.', 'success');
    } else {
      await partiesService.createCustomer(payload);
      notificationStore.show('Cliente registrado con éxito.', 'success');
    }

    router.push({ name: 'customers.index' });
  } catch (error: any) {
    notificationStore.show(
      error?.response?.data?.message || 'Error al guardar el cliente.',
      'error'
    );
  } finally {
    isSaving.value = false;
  }
}

onMounted(() => {
  if (isEditing.value && customerId.value) {
    loadCustomer(customerId.value);
  }
});
</script>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
</style>