<template>
  <div class="receivables-view w-100">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Cuentas por Cobrar (CxC)</h1>
        <p class="text-caption text-medium-emphasis">
          Control de créditos comerciales, amortización de saldos y vencimientos.
        </p>
      </div>

      <v-chip color="primary" variant="tonal" size="small" class="font-mono font-weight-bold mt-2 mt-sm-0">
        <v-icon start size="16">mdi-currency-usd</v-icon>
        Tasa: {{ exchangeRate.toFixed(2) }} VES
      </v-chip>
    </div>

    <!-- Tarjetas de Métricas / KPIs -->
    <v-row dense class="mb-4">
      <v-col cols="12" sm="4">
        <v-card elevation="0" border rounded="lg" class="pa-4">
          <div class="d-flex justify-space-between align-center mb-1">
            <span class="text-caption text-medium-emphasis font-weight-medium">Por Cobrar Total</span>
            <v-avatar color="primary" variant="tonal" size="28">
              <v-icon size="16">mdi-cash-clock</v-icon>
            </v-avatar>
          </div>
          <div class="text-h6 font-weight-bold font-mono text-primary">
            ${{ Number(stats.total_receivables).toFixed(2) }}
          </div>
          <div class="text-caption text-medium-emphasis font-mono">
            Bs. {{ (Number(stats.total_receivables) * exchangeRate).toLocaleString('es-VE', { minimumFractionDigits: 2 }) }}
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="4">
        <v-card elevation="0" border rounded="lg" class="pa-4">
          <div class="d-flex justify-space-between align-center mb-1">
            <span class="text-caption text-medium-emphasis font-weight-medium">Cartera Vencida (Mora)</span>
            <v-avatar color="error" variant="tonal" size="28">
              <v-icon size="16">mdi-alert-circle-outline</v-icon>
            </v-avatar>
          </div>
          <div class="text-h6 font-weight-bold font-mono text-error">
            ${{ Number(stats.overdue_amount).toFixed(2) }}
          </div>
          <div class="text-caption text-medium-emphasis font-mono">
            Bs. {{ (Number(stats.overdue_amount) * exchangeRate).toLocaleString('es-VE', { minimumFractionDigits: 2 }) }}
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="4">
        <v-card elevation="0" border rounded="lg" class="pa-4">
          <div class="d-flex justify-space-between align-center mb-1">
            <span class="text-caption text-medium-emphasis font-weight-medium">Recaudado este Mes</span>
            <v-avatar color="success" variant="tonal" size="28">
              <v-icon size="16">mdi-check-decagram-outline</v-icon>
            </v-avatar>
          </div>
          <div class="text-h6 font-weight-bold font-mono text-success">
            ${{ Number(stats.collected_mtd).toFixed(2) }}
          </div>
          <div class="text-caption text-medium-emphasis font-mono">
            Bs. {{ (Number(stats.collected_mtd) * exchangeRate).toLocaleString('es-VE', { minimumFractionDigits: 2 }) }}
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Tabla de Cuentas por Cobrar -->
    <v-card elevation="0" border rounded="lg">
      <div class="pa-4 d-flex flex-column flex-md-row justify-space-between align-center gap-3">
        <div class="d-flex flex-column flex-sm-row align-center gap-3 w-100" style="max-width: 640px;">
          <v-text-field
            v-model="searchQuery"
            placeholder="Buscar por cliente o factura..."
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
            v-model="selectedStatus"
            :items="statusOptions"
            item-title="label"
            item-value="value"
            placeholder="Estado de deuda"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="w-100"
            style="max-width: 200px;"
            @update:model-value="onFilterChange"
          />
        </div>

        <div class="d-flex align-center gap-2">
          <v-switch
            v-model="onlyWithBalance"
            label="Solo pendientes"
            color="primary"
            density="compact"
            hide-details
            @update:model-value="onFilterChange"
          />
          <v-btn
            variant="text"
            density="compact"
            icon="mdi-refresh"
            :loading="isLoading"
            title="Recargar datos"
            @click="loadAll"
          />
        </div>
      </div>

      <v-divider />

      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        v-model:sort-by="sortBy"
        :headers="headers"
        :items="receivables"
        :items-length="totalReceivables"
        :loading="isLoading"
        loading-text="Cargando cartera de créditos..."
        no-data-text="No hay cuentas por cobrar registradas con los filtros actuales."
        density="comfortable"
        hover
        @update:options="loadReceivables"
      >
        <!-- Cliente -->
        <template #item.customer="{ item }">
          <div class="py-2">
            <div class="font-weight-medium text-body-2">{{ item.customer?.name || 'Cliente desconocido' }}</div>
            <div class="text-caption text-medium-emphasis font-mono">
              {{ item.customer?.rif_ci || 'S/N' }} <span v-if="item.customer?.phone">&bull; {{ item.customer.phone }}</span>
            </div>
          </div>
        </template>

        <!-- Factura y Fechas -->
        <template #item.sale="{ item }">
          <div class="py-2 text-caption">
            <div class="font-mono font-weight-bold text-primary">{{ item.sale?.invoice_number || 'N/A' }}</div>
            <div class="text-medium-emphasis">Vence: {{ formatDate(item.due_date) }}</div>
          </div>
        </template>

        <!-- Total de la Venta -->
        <template #item.total="{ item }">
          <span class="font-mono text-body-2">${{ Number(item.total).toFixed(2) }}</span>
        </template>

        <!-- Amortizado -->
        <template #item.paid_amount="{ item }">
          <span class="font-mono text-body-2 text-success">${{ Number(item.paid_amount).toFixed(2) }}</span>
        </template>

        <!-- Saldo Deudor ($ y VES) -->
        <template #item.balance="{ item }">
          <div>
            <div class="text-body-2 font-weight-bold font-mono" :class="Number(item.balance) > 0 ? 'text-error' : 'text-success'">
              ${{ Number(item.balance).toFixed(2) }}
            </div>
            <div class="text-caption text-medium-emphasis font-mono">
              Bs. {{ (Number(item.balance) * exchangeRate).toFixed(2) }}
            </div>
          </div>
        </template>

        <!-- Estado de la Deuda -->
        <template #item.status="{ item }">
          <v-chip
            size="x-small"
            :color="resolveStatusColor(item)"
            variant="tonal"
            class="font-weight-medium"
          >
            {{ resolveStatusLabel(item) }}
          </v-chip>
        </template>

        <!-- Acciones -->
        <template #item.actions="{ item }">
          <div class="d-flex justify-end gap-1">
            <v-btn
              v-if="Number(item.balance) > 0"
              size="x-small"
              color="primary"
              variant="tonal"
              prepend-icon="mdi-cash-plus"
              @click="openPaymentModal(item)"
            >
              Abonar
            </v-btn>
            <v-btn
              icon="mdi-eye-outline"
              size="x-small"
              variant="text"
              color="medium-emphasis"
              title="Ver detalle e historial"
              @click="openDetails(item)"
            />
          </div>
        </template>
      </v-data-table-server>
    </v-card>

    <!-- DIÁLOGO: Registrar Abono -->
    <v-dialog v-model="paymentDialog" max-width="480" persistent>
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-4 d-flex align-center justify-space-between border-b">
          <span class="text-subtitle-1 font-weight-bold">Registrar Abono a Cuenta</span>
          <v-btn icon="mdi-close" variant="text" size="small" @click="paymentDialog = false" />
        </v-card-title>

        <v-card-text class="pa-4">
          <div v-if="selectedReceivable" class="pa-3 mb-3 border rounded-lg bg-surface-light">
            <div class="text-caption text-medium-emphasis">Cliente: <strong>{{ selectedReceivable.customer?.name }}</strong></div>
            <div class="text-caption text-medium-emphasis">Factura: <strong class="font-mono">{{ selectedReceivable.sale?.invoice_number }}</strong></div>
            <div class="d-flex justify-space-between align-baseline mt-2 pt-2 border-t">
              <span class="text-caption font-weight-bold">Saldo Pendiente:</span>
              <div class="text-right">
                <span class="font-mono text-subtitle-2 font-weight-bold text-error">
                  ${{ Number(selectedReceivable.balance).toFixed(2) }}
                </span>
                <span class="text-caption text-medium-emphasis ms-1 font-mono">
                  (Bs. {{ (Number(selectedReceivable.balance) * exchangeRate).toFixed(2) }})
                </span>
              </div>
            </div>
          </div>

          <v-form ref="paymentFormRef" @submit.prevent="submitPayment">
            <v-row dense>
              <!-- Selector de moneda de entrada para conveniencia del cajero -->
              <v-col cols="12" class="mb-2">
                <v-btn-toggle v-model="inputCurrency" mandatory color="primary" density="compact" class="w-100">
                  <v-btn value="USD" size="small" class="flex-grow-1">Abonar en Dólares ($)</v-btn>
                  <v-btn value="VES" size="small" class="flex-grow-1">Abonar en Bolívares (Bs.)</v-btn>
                </v-btn-toggle>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  v-model.number="paymentForm.amount_usd"
                  label="Monto ($ USD) *"
                  type="number"
                  min="0.01"
                  :max="Number(selectedReceivable?.balance || 0)"
                  step="0.01"
                  variant="outlined"
                  density="compact"
                  prefix="$"
                  :rules="[
                    (v: any) => v > 0 || 'El monto debe ser mayor a 0',
                    (v: any) => v <= Number(selectedReceivable?.balance || 0) || 'Excede el saldo deudor',
                  ]"
                  @update:model-value="onUsdAmountChange"
                />
              </v-col>

              <v-col cols="6">
                <v-text-field
                  v-model.number="amountVes"
                  label="Equivalente (Bs. VES)"
                  type="number"
                  min="0.01"
                  step="0.01"
                  variant="outlined"
                  density="compact"
                  prefix="Bs."
                  @update:model-value="onVesAmountChange"
                />
              </v-col>

              <v-col cols="6">
                <v-text-field
                  v-model="paymentForm.payment_date"
                  label="Fecha de Pago *"
                  type="date"
                  variant="outlined"
                  density="compact"
                />
              </v-col>

              <v-col cols="6">
                <v-select
                  v-model="paymentForm.payment_method"
                  :items="['Transferencia', 'Pago Móvil', 'Efectivo', 'Punto de Venta']"
                  label="Forma de Pago *"
                  variant="outlined"
                  density="compact"
                />
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="paymentForm.notes"
                  label="Referencia / Observación"
                  placeholder="Ej. Ref #8934 Banco Provincial"
                  variant="outlined"
                  density="compact"
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4 bg-surface-light">
          <v-spacer />
          <v-btn variant="text" size="small" :disabled="isSubmittingPayment" @click="paymentDialog = false">
            Cancelar
          </v-btn>
          <v-btn
            color="primary"
            size="small"
            :loading="isSubmittingPayment"
            prepend-icon="mdi-check"
            @click="submitPayment"
          >
            Aplicar Abono
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DRAWER: Detalle e Historial de Abonos -->
    <v-navigation-drawer v-model="drawer" location="right" temporary width="480">
      <div v-if="selectedReceivable" class="h-100 d-flex flex-column">
        <div class="pa-4 border-b d-flex justify-space-between align-center bg-surface-light">
          <div>
            <div class="text-caption text-medium-emphasis">Cuenta por Cobrar</div>
            <div class="text-subtitle-1 font-weight-bold font-mono">{{ selectedReceivable.sale?.invoice_number }}</div>
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="drawer = false" />
        </div>

        <div class="flex-grow-1 overflow-y-auto pa-4">
          <div class="text-caption mb-3">
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Cliente:</span>
              <span class="font-weight-medium">{{ selectedReceivable.customer?.name }}</span>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Fecha Emisión:</span>
              <span>{{ formatDate(selectedReceivable.sale_date) }}</span>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Fecha Vencimiento:</span>
              <span>{{ formatDate(selectedReceivable.due_date) }}</span>
            </div>
          </div>

          <v-divider class="my-3" />

          <!-- Resumen de Saldos -->
          <v-row dense class="mb-3">
            <v-col cols="4">
              <v-card elevation="0" border rounded="lg" class="pa-2 text-center bg-surface-light">
                <span class="text-caption text-medium-emphasis d-block">Total</span>
                <span class="font-mono font-weight-bold text-caption">${{ Number(selectedReceivable.total).toFixed(2) }}</span>
              </v-card>
            </v-col>
            <v-col cols="4">
              <v-card elevation="0" border rounded="lg" class="pa-2 text-center bg-surface-light">
                <span class="text-caption text-medium-emphasis d-block">Abonado</span>
                <span class="font-mono font-weight-bold text-caption text-success">${{ Number(selectedReceivable.paid_amount).toFixed(2) }}</span>
              </v-card>
            </v-col>
            <v-col cols="4">
              <v-card elevation="0" border rounded="lg" class="pa-2 text-center bg-surface-light">
                <span class="text-caption text-medium-emphasis d-block">Resta</span>
                <span class="font-mono font-weight-bold text-caption text-error">${{ Number(selectedReceivable.balance).toFixed(2) }}</span>
              </v-card>
            </v-col>
          </v-row>

          <v-divider class="my-3" />

          <!-- Histórico de Abonos -->
          <span class="text-caption font-weight-bold text-uppercase d-block mb-2 text-medium-emphasis">
            Histórico de Abonos
          </span>

          <div v-if="isLoadingDetails" class="py-4 text-center">
            <v-progress-circular indeterminate size="24" color="primary" />
          </div>

          <div v-else-if="!selectedReceivable.payments || selectedReceivable.payments.length === 0" class="pa-4 text-center text-caption text-medium-emphasis border rounded-lg bg-surface-light">
            No se han registrado abonos para esta factura.
          </div>

          <div v-else class="d-flex flex-column gap-2">
            <div
              v-for="p in selectedReceivable.payments"
              :key="p.id"
              class="pa-3 border rounded-lg bg-surface-light d-flex justify-space-between align-center"
            >
              <div>
                <div class="text-body-2 font-weight-medium">{{ p.payment_method }}</div>
                <div class="text-caption text-medium-emphasis">{{ formatDate(p.payment_date) }} &bull; Tasa: {{ Number(p.exchange_rate).toFixed(2) }}</div>
                <div v-if="p.notes" class="text-caption font-italic text-medium-emphasis">{{ p.notes }}</div>
              </div>
              <div class="text-right">
                <div class="font-mono font-weight-bold text-success">+${{ Number(p.amount_usd).toFixed(2) }}</div>
                <div class="text-caption text-medium-emphasis font-mono">
                  Bs. {{ (Number(p.amount_usd) * Number(p.exchange_rate)).toFixed(2) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import receivablesService from '@/api/receivables.api';
import settingsService from '@/api/settings.api';
import { useNotificationStore } from '@/stores/notification';
import type {
  AccountsReceivable,
  ReceivablesStats,
  ReceivableStatus,
  RecordPaymentPayload,
} from '@/types/receivables';

const notificationStore = useNotificationStore();

// Estados
const isLoading = ref(false);
const isLoadingDetails = ref(false);
const receivables = ref<AccountsReceivable[]>([]);
const totalReceivables = ref(0);
const exchangeRate = ref(1.0);

const stats = reactive<ReceivablesStats>({
  total_receivables: 0,
  overdue_amount: 0,
  collected_mtd: 0,
});

// Paginación y Filtros
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref<Array<{ key: string; order: 'asc' | 'desc' }>>([{ key: 'due_date', order: 'asc' }]);
const searchQuery = ref('');
const selectedStatus = ref<ReceivableStatus | null>(null);
const onlyWithBalance = ref(true);
let searchDebounce: ReturnType<typeof setTimeout> | null = null;

const statusOptions = [
  { label: 'Todos los estados', value: null },
  { label: 'Pendiente', value: 'pending' },
  { label: 'Abono parcial', value: 'partial' },
  { label: 'Vencida (Mora)', value: 'overdue' },
  { label: 'Liquidada', value: 'paid' },
];

const headers = [
  { title: 'Cliente', key: 'customer', sortable: false },
  { title: 'Factura / Vence', key: 'sale', sortable: true },
  { title: 'Total Venta', key: 'total', sortable: true },
  { title: 'Abonado', key: 'paid_amount', sortable: false },
  { title: 'Saldo Pendiente', key: 'balance', sortable: true },
  { title: 'Estado', key: 'status', sortable: false },
  { title: '', key: 'actions', sortable: false, align: 'end' as const },
];

// Diálogo de Abonos
const paymentDialog = ref(false);
const isSubmittingPayment = ref(false);
const paymentFormRef = ref<HTMLFormElement | null>(null);
const selectedReceivable = ref<AccountsReceivable | null>(null);
const inputCurrency = ref<'USD' | 'VES'>('USD');
const amountVes = ref<number | null>(null);

const paymentForm = reactive<RecordPaymentPayload>({
  amount_usd: 0,
  exchange_rate: 1.0,
  payment_date: new Date().toISOString().substring(0, 10),
  payment_method: 'Transferencia',
  notes: '',
});

// Drawer
const drawer = ref(false);

async function loadAll() {
  await Promise.all([loadStats(), loadExchangeRate(), loadReceivables()]);
}

async function loadExchangeRate() {
  try {
    const settings = await settingsService.getSettings();
    const rate = Number(settings['exchange_rate'] || settings['bcv_rate'] || 1.0);
    exchangeRate.value = rate > 0 ? rate : 1.0;
  } catch {
    exchangeRate.value = 1.0;
  }
}

async function loadStats() {
  try {
    const data = await receivablesService.getStats();
    stats.total_receivables = data.total_receivables;
    stats.overdue_amount = data.overdue_amount;
    stats.collected_mtd = data.collected_mtd;
  } catch {
    // Silencioso para no romper render de tabla
  }
}

async function loadReceivables() {
  isLoading.value = true;
  try {
    const sortField = sortBy.value.length ? sortBy.value[0].key : 'due_date';
    const sortDirection = sortBy.value.length ? sortBy.value[0].order : 'asc';

    const response = await receivablesService.getReceivables({
      page: page.value,
      per_page: itemsPerPage.value,
      search: searchQuery.value || undefined,
      status: selectedStatus.value || undefined,
      only_with_balance: onlyWithBalance.value,
      sort_field: sortField,
      sort_direction: sortDirection,
    });

    receivables.value = response.data;
    totalReceivables.value = response.total;
  } catch (error: any) {
    notificationStore.error(error?.response?.data?.message || 'Error al cargar cuentas por cobrar.');
  } finally {
    isLoading.value = false;
  }
}

function onSearchInput() {
  if (searchDebounce) clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    page.value = 1;
    loadReceivables();
  }, 350);
}

function onSearchClear() {
  searchQuery.value = '';
  page.value = 1;
  loadReceivables();
}

function onFilterChange() {
  page.value = 1;
  loadReceivables();
}

// Modal de Abono
function openPaymentModal(item: AccountsReceivable) {
  selectedReceivable.value = item;
  paymentForm.amount_usd = Number(item.balance);
  paymentForm.exchange_rate = exchangeRate.value;
  paymentForm.payment_date = new Date().toISOString().substring(0, 10);
  paymentForm.payment_method = 'Transferencia';
  paymentForm.notes = '';
  amountVes.value = Number((paymentForm.amount_usd * exchangeRate.value).toFixed(2));
  paymentDialog.value = true;
}

function onUsdAmountChange(val: number) {
  if (val >= 0) {
    amountVes.value = Number((val * exchangeRate.value).toFixed(2));
  }
}

function onVesAmountChange(val: number) {
  if (val >= 0 && exchangeRate.value > 0) {
    paymentForm.amount_usd = Number((val / exchangeRate.value).toFixed(2));
  }
}

async function submitPayment() {
  if (!selectedReceivable.value || !paymentFormRef.value) return;

  const { valid } = await paymentFormRef.value.validate();
  if (!valid) return;

  isSubmittingPayment.value = true;
  try {
    await receivablesService.recordPayment(selectedReceivable.value.id, {
      amount_usd: Number(paymentForm.amount_usd),
      exchange_rate: exchangeRate.value,
      payment_date: paymentForm.payment_date,
      payment_method: paymentForm.payment_method,
      notes: paymentForm.notes?.trim() || null,
    });

    notificationStore.success('¡Abono registrado y saldo actualizado con éxito!');
    paymentDialog.value = false;
    await loadAll();
  } catch (error: any) {
    notificationStore.error(error?.response?.data?.message || 'Error al asentar el abono.');
  } finally {
    isSubmittingPayment.value = false;
  }
}

// Drawer de Detalle
async function openDetails(item: AccountsReceivable) {
  selectedReceivable.value = item;
  drawer.value = true;
  isLoadingDetails.value = true;

  try {
    const full = await receivablesService.getReceivableById(item.id);
    selectedReceivable.value = full;
  } catch {
    notificationStore.warning('Error al consultar el detalle de pagos.');
  } finally {
    isLoadingDetails.value = false;
  }
}

// Helpers
function resolveStatusColor(item: AccountsReceivable): string {
  if (Number(item.balance) <= 0) return 'success';
  if (new Date(item.due_date).getTime() < Date.now()) return 'error';
  if (Number(item.paid_amount) > 0) return 'warning';
  return 'info';
}

function resolveStatusLabel(item: AccountsReceivable): string {
  if (Number(item.balance) <= 0) return 'Liquidada';
  if (new Date(item.due_date).getTime() < Date.now()) return 'Vencida';
  if (Number(item.paid_amount) > 0) return 'Parcial';
  return 'Pendiente';
}

function formatDate(dateStr: string): string {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleDateString('es-VE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
}

onMounted(() => {
  loadAll();
});
</script>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
</style>