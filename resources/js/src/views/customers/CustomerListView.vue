<template>
  <div class="customer-list-view w-100">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Directorio de Clientes</h1>
        <p class="text-caption text-medium-emphasis">
          Gestione la cartera comercial, datos fiscales y cuentas de clientes.
        </p>
      </div>

      <v-btn
        color="primary"
        size="small"
        prepend-icon="mdi-plus"
        class="mt-3 mt-sm-0"
        :to="{ name: 'customers.create' }"
      >
        Nuevo Cliente
      </v-btn>
    </div>

    <!-- Contenedor Principal: Filtros y Tabla -->
    <v-card elevation="0" border rounded="lg">
      <div class="pa-4 d-flex flex-column flex-md-row justify-space-between align-center gap-3">
        <!-- Buscador y Filtro por Tipo -->
        <div class="d-flex flex-column flex-sm-row align-center gap-3 w-100" style="max-width: 650px;">
          <v-text-field
            v-model="searchQuery"
            placeholder="Buscar por RIF, cédula, nombre o correo..."
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="w-100"
            @update:model-value="onSearchInput"
            @click:clear="onSearchClear"
          />

          <v-select
            v-model="selectedType"
            :items="typeOptions"
            item-title="label"
            item-value="value"
            placeholder="Tipo de cliente"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="w-100"
            style="max-width: 200px;"
            @update:model-value="onTypeChange"
          />
        </div>

        <v-btn
          variant="text"
          density="compact"
          icon="mdi-refresh"
          :loading="isLoading"
          title="Recargar directorio"
          @click="loadCustomers"
        />
      </div>

      <v-divider />

      <!-- Data Table Server -->
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        v-model:sort-by="sortBy"
        :headers="headers"
        :items="customers"
        :items-length="totalCustomers"
        :loading="isLoading"
        loading-text="Cargando directorio de clientes..."
        no-data-text="No se encontraron clientes registrados."
        density="comfortable"
        hover
        @update:options="loadCustomers"
      >
        <!-- Columna: Cliente (Avatar con iniciales, Nombre y RIF) -->
        <template #item.name="{ item }">
          <div class="d-flex align-center gap-3 py-2">
            <v-avatar
              :color="item.type === 'Corporate' ? 'primary' : 'secondary'"
              variant="tonal"
              size="36"
              rounded="circle"
              class="font-weight-bold text-caption"
            >
              {{ item.initials || 'C' }}
            </v-avatar>

            <div>
              <div class="font-weight-medium text-body-2">{{ item.name }}</div>
              <div class="text-caption text-medium-emphasis font-mono">
                {{ item.rif_ci }}
              </div>
            </div>
          </div>
        </template>

        <!-- Columna: Tipo de Cliente -->
        <template #item.type="{ item }">
          <v-chip
            size="x-small"
            :color="item.type === 'Corporate' ? 'primary' : 'surface-variant'"
            variant="tonal"
            class="font-weight-medium"
          >
            {{ item.type === 'Corporate' ? 'Jurídico / Empresa' : 'Natural / Persona' }}
          </v-chip>
        </template>

        <!-- Columna: Contacto -->
        <template #item.contact="{ item }">
          <div class="text-caption">
            <div v-if="item.phone" class="d-flex align-center gap-1 text-medium-emphasis">
              <v-icon size="14">mdi-phone-outline</v-icon>
              <span>{{ item.phone }}</span>
            </div>
            <div v-if="item.email" class="d-flex align-center gap-1 text-medium-emphasis">
              <v-icon size="14">mdi-email-outline</v-icon>
              <span>{{ item.email }}</span>
            </div>
            <span v-if="!item.phone && !item.email" class="text-medium-emphasis">—</span>
          </div>
        </template>

        <!-- Columna: Ventas Asociadas -->
        <template #item.sales_count="{ item }">
          <v-chip
            size="small"
            :color="item.sales_count && item.sales_count > 0 ? 'info' : 'surface-variant'"
            variant="tonal"
            class="font-weight-medium"
          >
            {{ item.sales_count || 0 }} transacciones
          </v-chip>
        </template>

        <!-- Columna: Acciones -->
        <template #item.actions="{ item }">
          <div class="d-flex justify-end gap-1">
            <v-btn
              icon="mdi-pencil-outline"
              size="x-small"
              variant="text"
              color="primary"
              title="Editar"
              :to="{ name: 'customers.edit', params: { id: item.id } }"
            />
            <v-btn
              icon="mdi-trash-can-outline"
              size="x-small"
              variant="text"
              color="error"
              title="Eliminar"
              @click="confirmDelete(item)"
            />
          </div>
        </template>
      </v-data-table-server>
    </v-card>

    <!-- Diálogo Ligero de Eliminación Defensiva -->
    <v-dialog v-model="deleteDialog" max-width="440" persistent>
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-5 pb-3 d-flex align-center gap-2">
          <v-icon color="warning" size="24">mdi-alert-circle-outline</v-icon>
          <span class="text-subtitle-1 font-weight-bold">Confirmar Eliminación</span>
        </v-card-title>

        <v-card-text class="px-5 py-3">
          <p class="text-body-2 mb-2">
            ¿Está seguro de que desea eliminar a <strong>{{ targetCustomer?.name }}</strong>?
          </p>

          <v-alert
            v-if="targetCustomer && (targetCustomer.sales_count ?? 0) > 0"
            type="error"
            variant="tonal"
            density="compact"
            rounded="lg"
            class="text-caption mt-2"
          >
            Este cliente tiene {{ targetCustomer.sales_count }} ventas asociadas. La operación será rechazada por el servidor para proteger el historial contable.
          </v-alert>
          <p v-else class="text-caption text-medium-emphasis">
            Esta acción enviará al cliente a la papelera (soft delete).
          </p>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4 bg-surface-light">
          <v-spacer />
          <v-btn variant="text" size="small" :disabled="isDeleting" @click="closeDeleteDialog">
            Cancelar
          </v-btn>
          <v-btn
            color="error"
            size="small"
            :loading="isDeleting"
            prepend-icon="mdi-trash-can-outline"
            @click="executeDelete"
          >
            Eliminar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import partiesService from '@/api/parties.api';
import { useNotificationStore } from '@/stores/notification';
import type { Customer, CustomerType } from '@/types/parties';

const notificationStore = useNotificationStore();

const customers = ref<Customer[]>([]);
const totalCustomers = ref(0);
const isLoading = ref(false);
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref<Array<{ key: string; order: 'asc' | 'desc' }>>([{ key: 'created_at', order: 'desc' }]);

const searchQuery = ref('');
const selectedType = ref<CustomerType | null>(null);
let searchDebounceTimeout: ReturnType<typeof setTimeout> | null = null;

const typeOptions = [
  { label: 'Todos los tipos', value: null },
  { label: 'Natural / Individual', value: 'Individual' },
  { label: 'Jurídico / Corporate', value: 'Corporate' },
];

const headers = [
  { title: 'Cliente / Documento', key: 'name', sortable: true },
  { title: 'Clasificación', key: 'type', sortable: false },
  { title: 'Contacto', key: 'contact', sortable: false },
  { title: 'Historial', key: 'sales_count', sortable: false, align: 'center' as const },
  { title: 'Acciones', key: 'actions', sortable: false, align: 'end' as const },
];

// Diálogo de eliminación
const deleteDialog = ref(false);
const isDeleting = ref(false);
const targetCustomer = ref<Customer | null>(null);

async function loadCustomers() {
  isLoading.value = true;
  try {
    const sortField = sortBy.value.length ? sortBy.value[0].key : 'created_at';
    const sortDirection = sortBy.value.length ? sortBy.value[0].order : 'desc';

    const response = await partiesService.getCustomers({
      page: page.value,
      per_page: itemsPerPage.value,
      search: searchQuery.value || undefined,
      type: selectedType.value || undefined,
      sort_field: sortField,
      sort_direction: sortDirection,
    });

    customers.value = response.data;
    totalCustomers.value = response.total;
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al cargar los clientes.'
    );
  } finally {
    isLoading.value = false;
  }
}

function onSearchInput() {
  if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
  searchDebounceTimeout = setTimeout(() => {
    page.value = 1;
    loadCustomers();
  }, 350);
}

function onSearchClear() {
  searchQuery.value = '';
  page.value = 1;
  loadCustomers();
}

function onTypeChange() {
  page.value = 1;
  loadCustomers();
}

function confirmDelete(customer: Customer) {
  targetCustomer.value = customer;
  deleteDialog.value = true;
}

function closeDeleteDialog() {
  deleteDialog.value = false;
  targetCustomer.value = null;
}

async function executeDelete() {
  if (!targetCustomer.value) return;

  isDeleting.value = true;
  try {
    await partiesService.deleteCustomer(targetCustomer.value.id);
    notificationStore.success('Cliente eliminado del sistema.');
    closeDeleteDialog();

    if (customers.value.length === 1 && page.value > 1) {
      page.value -= 1;
    }
    await loadCustomers();
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'No fue posible eliminar el cliente.'
    );
  } finally {
    isDeleting.value = false;
  }
}
</script>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
</style>