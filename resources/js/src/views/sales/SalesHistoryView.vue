<template>
  <div class="sales-history-view w-100">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Historial de Ventas</h1>
        <p class="text-caption text-medium-emphasis">
          Auditoría de transacciones comerciales, facturas emitidas y recaudación por método de pago.
        </p>
      </div>

      <v-btn
        color="primary"
        size="small"
        prepend-icon="mdi-point-of-sale"
        class="mt-3 mt-sm-0"
        :to="{ name: 'pos.terminal' }"
      >
        Ir a Terminal POS
      </v-btn>
    </div>

    <!-- Contenedor Principal: Filtros y Tabla -->
    <v-card elevation="0" border rounded="lg">
      <div class="pa-4 d-flex flex-column flex-md-row justify-space-between align-center gap-3">
        <!-- Buscador y Selectores de Filtrado -->
        <div class="d-flex flex-column flex-sm-row align-center gap-3 w-100" style="max-width: 720px;">
          <v-text-field
            v-model="searchQuery"
            placeholder="Buscar por correlativo (NX-...) o cliente..."
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
            v-model="selectedPaymentMethod"
            :items="paymentMethodOptions"
            item-title="label"
            item-value="value"
            placeholder="Método de pago"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="w-100"
            style="max-width: 200px;"
            @update:model-value="onFilterChange"
          />

          <v-select
            v-model="selectedStatus"
            :items="statusOptions"
            item-title="label"
            item-value="value"
            placeholder="Estado"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="w-100"
            style="max-width: 170px;"
            @update:model-value="onFilterChange"
          />
        </div>

        <v-btn
          variant="text"
          density="compact"
          icon="mdi-refresh"
          :loading="isLoading"
          title="Recargar historial"
          @click="loadSales"
        />
      </div>

      <v-divider />

      <!-- Data Table Server -->
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        v-model:sort-by="sortBy"
        :headers="headers"
        :items="sales"
        :items-length="totalSales"
        :loading="isLoading"
        loading-text="Cargando historial de ventas..."
        no-data-text="No se encontraron transacciones registradas."
        density="comfortable"
        hover
        @update:options="loadSales"
      >
        <!-- Columna: Factura / Correlativo -->
        <template #item.invoice_number="{ item }">
          <div class="py-2">
            <span class="font-mono font-weight-bold text-body-2 text-primary">
              {{ item.invoice_number }}
            </span>
            <div class="text-caption text-medium-emphasis">
              {{ formatDate(item.sale_date) }}
            </div>
          </div>
        </template>

        <!-- Columna: Cliente -->
        <template #item.customer="{ item }">
          <div>
            <div class="font-weight-medium text-body-2">
              {{ item.customer?.name || 'Cliente Ocasional' }}
            </div>
            <div v-if="item.customer?.rif_ci" class="text-caption text-medium-emphasis font-mono">
              {{ item.customer.rif_ci }}
            </div>
          </div>
        </template>

        <!-- Columna: Método de Pago -->
        <template #item.payment_method="{ item }">
          <v-chip
            size="x-small"
            :color="getPaymentMethodColor(item.payment_method)"
            variant="tonal"
            class="font-weight-medium text-capitalize"
          >
            {{ translatePaymentMethod(item.payment_method) }}
          </v-chip>
        </template>

        <!-- Columna: Tasa Histórica -->
        <template #item.exchange_rate="{ item }">
          <span class="font-mono text-caption text-medium-emphasis">
            {{ Number(item.exchange_rate).toFixed(2) }} VES
          </span>
        </template>

        <!-- Columna: Total ($ y VES al cambio de la operación) -->
        <template #item.total="{ item }">
          <div>
            <div class="text-body-2 font-weight-bold">
              ${{ Number(item.total).toFixed(2) }}
            </div>
            <div class="text-caption text-medium-emphasis">
              Bs. {{ (Number(item.total) * Number(item.exchange_rate)).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
            </div>
          </div>
        </template>

        <!-- Columna: Estado -->
        <template #item.status="{ item }">
          <v-chip
            size="x-small"
            :color="item.status === 'completed' ? 'success' : 'error'"
            variant="tonal"
            class="text-capitalize"
          >
            {{ item.status === 'completed' ? 'Completada' : item.status }}
          </v-chip>
        </template>

        <!-- Columna: Acciones -->
        <template #item.actions="{ item }">
          <v-btn
            icon="mdi-eye-outline"
            size="x-small"
            variant="text"
            color="primary"
            title="Ver detalle del comprobante"
            @click="openDetails(item)"
          />
        </template>
      </v-data-table-server>
    </v-card>

    <!-- Drawer Lateral: Detalle de la Venta / Recibo -->
    <v-navigation-drawer
      v-model="drawer"
      location="right"
      temporary
      width="460"
    >
      <div v-if="selectedSale" class="h-100 d-flex flex-column">
        <!-- Encabezado del Detalle -->
        <div class="pa-4 border-b d-flex justify-space-between align-center bg-surface-light">
          <div>
            <div class="text-caption text-medium-emphasis">Comprobante de Venta</div>
            <div class="text-h6 font-weight-bold font-mono">{{ selectedSale.invoice_number }}</div>
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="drawer = false" />
        </div>

        <!-- Cuerpo del Detalle -->
        <div class="flex-grow-1 overflow-y-auto pa-4">
          <!-- Metadatos de la Venta -->
          <div class="mb-4 text-caption">
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Fecha de emisión:</span>
              <span>{{ formatDate(selectedSale.sale_date) }}</span>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Cliente:</span>
              <span class="font-weight-medium">{{ selectedSale.customer?.name || 'Cliente Ocasional' }}</span>
            </div>
            <div v-if="selectedSale.customer?.rif_ci" class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Identificación:</span>
              <span class="font-mono">{{ selectedSale.customer.rif_ci }}</span>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Método de pago:</span>
              <span class="font-weight-medium text-capitalize">{{ translatePaymentMethod(selectedSale.payment_method) }}</span>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Tasa aplicada:</span>
              <span class="font-mono">{{ Number(selectedSale.exchange_rate).toFixed(2) }} VES</span>
            </div>
          </div>

          <v-divider class="my-3" />

          <!-- Artículos Vendidos -->
          <span class="text-caption font-weight-bold text-uppercase d-block mb-2 text-medium-emphasis">
            Renglones Facturados
          </span>

          <div v-if="isLoadingDetails" class="py-4 text-center">
            <v-progress-circular indeterminate size="24" color="primary" />
          </div>

          <div v-else class="d-flex flex-column gap-2 mb-4">
            <div
              v-for="item in detailedItems"
              :key="item.id"
              class="pa-3 border rounded-lg bg-surface-light d-flex justify-space-between align-center"
            >
              <div class="flex-grow-1 pr-2">
                <div class="text-body-2 font-weight-medium">{{ item.product?.name || 'Producto' }}</div>
                <div class="text-caption text-medium-emphasis">
                  {{ item.quantity }} x ${{ Number(item.price).toFixed(2) }}
                </div>
              </div>
              <div class="text-body-2 font-weight-bold font-mono">
                ${{ Number(item.subtotal).toFixed(2) }}
              </div>
            </div>
          </div>

          <v-divider class="my-3" />

          <!-- Resumen de Liquidación -->
          <div class="text-caption">
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Subtotal:</span>
              <span>${{ Number(selectedSale.subtotal).toFixed(2) }}</span>
            </div>
            <div v-if="Number(selectedSale.total_discount) > 0" class="d-flex justify-space-between py-1 text-success">
              <span>Descuento:</span>
              <span>-${{ Number(selectedSale.total_discount).toFixed(2) }}</span>
            </div>
            <div class="d-flex justify-space-between align-baseline pt-2 border-t mt-2">
              <span class="text-subtitle-2 font-weight-bold">Total Liquidado:</span>
              <div class="text-right">
                <div class="text-subtitle-1 font-weight-bold text-primary font-mono">
                  ${{ Number(selectedSale.total).toFixed(2) }}
                </div>
                <div class="text-caption text-medium-emphasis">
                  Bs. {{ (Number(selectedSale.total) * Number(selectedSale.exchange_rate)).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                </div>
              </div>
            </div>

            <!-- Notas de la Venta -->
            <div v-if="selectedSale.notes" class="mt-4 pa-3 bg-surface border rounded-lg">
              <span class="text-caption font-weight-bold d-block text-medium-emphasis mb-1">Observaciones:</span>
              <p class="text-caption mb-0">{{ selectedSale.notes }}</p>
            </div>
          </div>
        </div>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import salesService from '@/api/sale.api';
import { useNotificationStore } from '@/stores/notification';
import type { Sale, SaleItem, PaymentMethod, SaleStatus } from '@/types/sales';

const notificationStore = useNotificationStore();

// Estados de la tabla
const sales = ref<Sale[]>([]);
const totalSales = ref(0);
const isLoading = ref(false);
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref<Array<{ key: string; order: 'asc' | 'desc' }>>([{ key: 'sale_date', order: 'desc' }]);

// Filtros
const searchQuery = ref('');
const selectedPaymentMethod = ref<PaymentMethod | null>(null);
const selectedStatus = ref<SaleStatus | null>(null);
let searchDebounceTimeout: ReturnType<typeof setTimeout> | null = null;

const paymentMethodOptions = [
  { label: 'Todos los métodos', value: null },
  { label: 'Efectivo', value: 'cash' },
  { label: 'Tarjeta / Débito', value: 'card' },
  { label: 'Crédito', value: 'credit' },
];

const statusOptions = [
  { label: 'Todos los estados', value: null },
  { label: 'Completada', value: 'completed' },
  { label: 'Cancelada', value: 'cancelled' },
];

const headers = [
  { title: 'Comprobante / Fecha', key: 'invoice_number', sortable: true },
  { title: 'Cliente', key: 'customer', sortable: false },
  { title: 'Método', key: 'payment_method', sortable: false },
  { title: 'Tasa BCV', key: 'exchange_rate', sortable: false },
  { title: 'Total Liquidado', key: 'total', sortable: true },
  { title: 'Estado', key: 'status', sortable: false },
  { title: '', key: 'actions', sortable: false, align: 'end' as const },
];

// Detalle lateral (Drawer)
const drawer = ref(false);
const selectedSale = ref<Sale | null>(null);
const detailedItems = ref<SaleItem[]>([]);
const isLoadingDetails = ref(false);

async function loadSales() {
  isLoading.value = true;
  try {
    const sortField = sortBy.value.length ? sortBy.value[0].key : 'sale_date';
    const sortDirection = sortBy.value.length ? sortBy.value[0].order : 'desc';

    const response = await salesService.getSales({
      page: page.value,
      per_page: itemsPerPage.value,
      search: searchQuery.value || undefined,
      payment_method: selectedPaymentMethod.value || undefined,
      status: selectedStatus.value || undefined,
      sort_field: sortField,
      sort_direction: sortDirection,
    });

    sales.value = response.data;
    totalSales.value = response.total;
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al obtener el historial de ventas.'
    );
  } finally {
    isLoading.value = false;
  }
}

function onSearchInput() {
  if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
  searchDebounceTimeout = setTimeout(() => {
    page.value = 1;
    loadSales();
  }, 350);
}

function onSearchClear() {
  searchQuery.value = '';
  page.value = 1;
  loadSales();
}

function onFilterChange() {
  page.value = 1;
  loadSales();
}

async function openDetails(sale: Sale) {
  selectedSale.value = sale;
  detailedItems.value = sale.items || [];
  drawer.value = true;

  // Si los ítems no venían precargados en la fila, consultamos el endpoint show
  if (!sale.items || sale.items.length === 0) {
    isLoadingDetails.value = true;
    try {
      const fullSale = await salesService.getSaleById(sale.id);
      detailedItems.value = fullSale.items || [];
    } catch {
      notificationStore.warning('Error al consultar renglones de la venta.');
    } finally {
      isLoadingDetails.value = false;
    }
  }
}

// Helpers de formato y visualización
function formatDate(dateStr: string): string {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  return d.toLocaleString('es-VE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

function getPaymentMethodColor(method: PaymentMethod): string {
  switch (method) {
    case 'cash': return 'success';
    case 'card': return 'info';
    case 'credit': return 'warning';
    default: return 'surface-variant';
  }
}

function translatePaymentMethod(method: PaymentMethod): string {
  switch (method) {
    case 'cash': return 'Efectivo';
    case 'card': return 'Tarjeta';
    case 'credit': return 'Crédito';
    default: return method;
  }
}
</script>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
</style>