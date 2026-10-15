<template>
  <div class="shift-history-view w-100">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Historial de Turnos de Caja</h1>
        <p class="text-caption text-medium-emphasis">
          Auditoría de aperturas, cierres, fondos base y discrepancias de arqueo por cajero.
        </p>
      </div>

      <v-btn
        color="primary"
        size="small"
        prepend-icon="mdi-cash-register"
        class="mt-3 mt-sm-0"
        :to="{ name: 'cash-register.shift' }"
      >
        Turno en Curso
      </v-btn>
    </div>

    <!-- Contenedor Principal: Filtros y Tabla -->
    <v-card elevation="0" border rounded="lg">
      <div class="pa-4 d-flex flex-column flex-sm-row justify-space-between align-center gap-3">
        <!-- Selector de Filtro por Estado -->
        <div class="d-flex align-center gap-3 w-100" style="max-width: 320px;">
          <v-select
            v-model="selectedStatus"
            :items="statusOptions"
            item-title="label"
            item-value="value"
            placeholder="Filtrar por estado"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="w-100"
            @update:model-value="onFilterChange"
          />
        </div>

        <v-btn
          variant="text"
          density="compact"
          icon="mdi-refresh"
          :loading="isLoading"
          title="Recargar historial"
          @click="loadShifts"
        />
      </div>

      <v-divider />

      <!-- Data Table Server -->
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        v-model:sort-by="sortBy"
        :headers="headers"
        :items="shifts"
        :items-length="totalShifts"
        :loading="isLoading"
        loading-text="Cargando historial de turnos..."
        no-data-text="No se encontraron registros de turnos de caja."
        density="comfortable"
        hover
        @update:options="loadShifts"
      >
        <!-- Columna: Cajero Responsable -->
        <template #item.user="{ item }">
          <div class="py-2">
            <div class="font-weight-medium text-body-2">{{ item.user?.name || 'Usuario desconocido' }}</div>
            <div class="text-caption text-medium-emphasis">{{ item.user?.email || '—' }}</div>
          </div>
        </template>

        <!-- Columna: Intervalo Temporal -->
        <template #item.opened_at="{ item }">
          <div class="py-2 text-caption">
            <div>
              <span class="text-medium-emphasis">Apertura:</span>
              <span class="font-weight-medium ms-1">{{ formatDate(item.opened_at) }}</span>
            </div>
            <div>
              <span class="text-medium-emphasis">Cierre:</span>
              <span class="font-weight-medium ms-1">{{ item.closed_at ? formatDate(item.closed_at) : 'En curso' }}</span>
            </div>
          </div>
        </template>

        <!-- Columna: Fondo Base Inicial -->
        <template #item.initial_cash="{ item }">
          <div class="text-caption font-mono">
            <div>${{ Number(item.initial_cash_usd).toFixed(2) }}</div>
            <div class="text-medium-emphasis">Bs. {{ Number(item.initial_cash_ves).toFixed(2) }}</div>
          </div>
        </template>

        <!-- Columna: Arqueo Declarado (Real) -->
        <template #item.actual_cash="{ item }">
          <div v-if="item.status === 'closed'" class="text-caption font-mono">
            <div>${{ Number(item.actual_cash_usd ?? 0).toFixed(2) }}</div>
            <div class="text-medium-emphasis">Bs. {{ Number(item.actual_cash_ves ?? 0).toFixed(2) }}</div>
          </div>
          <span v-else class="text-caption text-medium-emphasis italic">Sin liquidar</span>
        </template>

        <!-- Columna: Diferencia / Discrepancia -->
        <template #item.difference="{ item }">
          <div v-if="item.status === 'closed'" class="text-caption font-mono font-weight-bold">
            <div :class="getDifferenceColor(Number(item.difference_usd))">
              {{ formatDifference(Number(item.difference_usd), '$') }}
            </div>
            <div :class="getDifferenceColor(Number(item.difference_ves))">
              {{ formatDifference(Number(item.difference_ves), 'Bs. ') }}
            </div>
          </div>
          <span v-else class="text-caption text-medium-emphasis">—</span>
        </template>

        <!-- Columna: Estado -->
        <template #item.status="{ item }">
          <v-chip
            size="x-small"
            :color="item.status === 'open' ? 'success' : 'surface-variant'"
            variant="tonal"
            class="font-weight-medium text-capitalize"
          >
            {{ item.status === 'open' ? 'Abierto' : 'Cerrado' }}
          </v-chip>
        </template>

        <!-- Columna: Acciones -->
        <template #item.actions="{ item }">
          <v-btn
            icon="mdi-eye-outline"
            size="x-small"
            variant="text"
            color="primary"
            title="Ver detalle del arqueo"
            @click="openDetails(item)"
          />
        </template>
      </v-data-table-server>
    </v-card>

    <!-- Drawer Lateral: Detalle Completo del Arqueo -->
    <v-navigation-drawer
      v-model="drawer"
      location="right"
      temporary
      width="460"
    >
      <div v-if="selectedShift" class="h-100 d-flex flex-column">
        <!-- Encabezado del Detalle -->
        <div class="pa-4 border-b d-flex justify-space-between align-center bg-surface-light">
          <div>
            <div class="text-caption text-medium-emphasis">Arqueo de Turno</div>
            <div class="text-subtitle-1 font-weight-bold">{{ selectedShift.user?.name || 'Cajero' }}</div>
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="drawer = false" />
        </div>

        <!-- Cuerpo del Detalle -->
        <div class="flex-grow-1 overflow-y-auto pa-4">
          <!-- Metadatos Básicos -->
          <div class="mb-4 text-caption">
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Estado:</span>
              <v-chip size="x-small" :color="selectedShift.status === 'open' ? 'success' : 'surface-variant'" variant="tonal">
                {{ selectedShift.status === 'open' ? 'Abierto' : 'Cerrado' }}
              </v-chip>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Fecha de Apertura:</span>
              <span>{{ formatDate(selectedShift.opened_at) }}</span>
            </div>
            <div v-if="selectedShift.closed_at" class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Fecha de Cierre:</span>
              <span>{{ formatDate(selectedShift.closed_at) }}</span>
            </div>
          </div>

          <v-divider class="my-3" />

          <!-- Desglose Financiero Bimoneda -->
          <span class="text-caption font-weight-bold text-uppercase d-block mb-3 text-medium-emphasis">
            Conciliación de Cierre
          </span>

          <v-row class="mb-3">
            <!-- USD -->
            <v-col cols="6">
              <v-card elevation="0" border rounded="lg" class="pa-3 bg-surface-light h-100">
                <span class="text-caption font-weight-bold text-primary d-block mb-2">Divisas ($)</span>
                <div class="text-caption font-mono">
                  <div class="text-medium-emphasis">Base: ${{ Number(selectedShift.initial_cash_usd).toFixed(2) }}</div>
                  <div class="text-medium-emphasis">Esperado: ${{ Number(selectedShift.expected_cash_usd).toFixed(2) }}</div>
                  <div class="font-weight-bold mt-1">Real: ${{ Number(selectedShift.actual_cash_usd ?? 0).toFixed(2) }}</div>
                  <div class="font-weight-bold mt-1" :class="getDifferenceColor(Number(selectedShift.difference_usd))">
                    Dif: {{ formatDifference(Number(selectedShift.difference_usd), '$') }}
                  </div>
                </div>
              </v-card>
            </v-col>

            <!-- VES -->
            <v-col cols="6">
              <v-card elevation="0" border rounded="lg" class="pa-3 bg-surface-light h-100">
                <span class="text-caption font-weight-bold text-secondary d-block mb-2">Bolívares (Bs.)</span>
                <div class="text-caption font-mono">
                  <div class="text-medium-emphasis">Base: Bs. {{ Number(selectedShift.initial_cash_ves).toFixed(2) }}</div>
                  <div class="text-medium-emphasis">Esperado: Bs. {{ Number(selectedShift.expected_cash_ves).toFixed(2) }}</div>
                  <div class="font-weight-bold mt-1">Real: Bs. {{ Number(selectedShift.actual_cash_ves ?? 0).toFixed(2) }}</div>
                  <div class="font-weight-bold mt-1" :class="getDifferenceColor(Number(selectedShift.difference_ves))">
                    Dif: {{ formatDifference(Number(selectedShift.difference_ves), 'Bs. ') }}
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>

          <v-divider class="my-3" />

          <!-- Movimientos Manuales Asociados -->
          <span class="text-caption font-weight-bold text-uppercase d-block mb-2 text-medium-emphasis">
            Movimientos Manuales Registrados
          </span>

          <div v-if="isLoadingDetails" class="py-4 text-center">
            <v-progress-circular indeterminate size="24" color="primary" />
          </div>

          <div v-else-if="detailedMovements.length === 0" class="pa-4 text-center text-caption text-medium-emphasis border rounded-lg bg-surface-light">
            No se realizaron ingresos o retiros extraordinarios en este turno.
          </div>

          <div v-else class="d-flex flex-column gap-2 mb-4">
            <div
              v-for="movement in detailedMovements"
              :key="movement.id"
              class="pa-3 border rounded-lg bg-surface-light d-flex justify-space-between align-center"
            >
              <div class="flex-grow-1 pr-2">
                <div class="d-flex align-center gap-1 mb-1">
                  <v-chip
                    size="x-small"
                    :color="movement.type === 'in' ? 'success' : 'error'"
                    variant="tonal"
                  >
                    {{ movement.type === 'in' ? 'Entrada' : 'Salida' }}
                  </v-chip>
                  <span class="text-caption font-mono text-medium-emphasis">{{ movement.currency }}</span>
                </div>
                <div class="text-body-2">{{ movement.concept }}</div>
                <div v-if="movement.notes" class="text-caption text-medium-emphasis font-italic">
                  {{ movement.notes }}
                </div>
              </div>
              <div
                class="text-body-2 font-weight-bold font-mono"
                :class="movement.type === 'in' ? 'text-success' : 'text-error'"
              >
                {{ movement.type === 'in' ? '+' : '-' }}{{ movement.currency === 'USD' ? '$' : 'Bs. ' }}{{ Number(movement.amount).toFixed(2) }}
              </div>
            </div>
          </div>

          <!-- Observaciones / Notas del Turno -->
          <div v-if="selectedShift.notes" class="mt-4 pa-3 bg-surface border rounded-lg">
            <span class="text-caption font-weight-bold d-block text-medium-emphasis mb-1">Observaciones:</span>
            <p class="text-caption mb-0">{{ selectedShift.notes }}</p>
          </div>
        </div>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import cashRegisterService from '@/api/cashRegister.api';
import { useNotificationStore } from '@/stores/notification';
import type { CashShift, CashMovement, CashShiftStatus } from '@/types/cashRegister';

const notificationStore = useNotificationStore();

// Estados de la tabla
const shifts = ref<CashShift[]>([]);
const totalShifts = ref(0);
const isLoading = ref(false);
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref<Array<{ key: string; order: 'asc' | 'desc' }>>([{ key: 'opened_at', order: 'desc' }]);

// Filtro
const selectedStatus = ref<CashShiftStatus | null>(null);

const statusOptions = [
  { label: 'Todos los turnos', value: null },
  { label: 'Abiertos', value: 'open' },
  { label: 'Cerrados', value: 'closed' },
];

const headers = [
  { title: 'Cajero', key: 'user', sortable: false },
  { title: 'Horario (Apertura / Cierre)', key: 'opened_at', sortable: true },
  { title: 'Fondo Base', key: 'initial_cash', sortable: false },
  { title: 'Arqueo Real', key: 'actual_cash', sortable: false },
  { title: 'Discrepancia', key: 'difference', sortable: false },
  { title: 'Estado', key: 'status', sortable: false },
  { title: '', key: 'actions', sortable: false, align: 'end' as const },
];

// Detalle lateral (Drawer)
const drawer = ref(false);
const selectedShift = ref<CashShift | null>(null);
const detailedMovements = ref<CashMovement[]>([]);
const isLoadingDetails = ref(false);

async function loadShifts() {
  isLoading.value = true;
  try {
    const sortField = sortBy.value.length ? sortBy.value[0].key : 'opened_at';
    const sortDirection = sortBy.value.length ? sortBy.value[0].order : 'desc';

    const response = await cashRegisterService.getShifts({
      page: page.value,
      per_page: itemsPerPage.value,
      status: selectedStatus.value || undefined,
      sort_field: sortField,
      sort_direction: sortDirection,
    });

    shifts.value = response.data;
    totalShifts.value = response.total;
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al obtener el historial de turnos.',
    );
  } finally {
    isLoading.value = false;
  }
}

function onFilterChange() {
  page.value = 1;
  loadShifts();
}

async function openDetails(shift: CashShift) {
  selectedShift.value = shift;
  detailedMovements.value = shift.movements || [];
  drawer.value = true;

  // Si los movimientos no venían hidratados en la lista, consultamos el summary
  if (!shift.movements || shift.movements.length === 0) {
    isLoadingDetails.value = true;
    try {
      const summaryData = await cashRegisterService.getShiftSummary(shift.id);
      detailedMovements.value = summaryData.shift.movements || [];
    } catch {
      notificationStore.warning('Error al consultar movimientos del turno.');
    } finally {
      isLoadingDetails.value = false;
    }
  }
}

// Helpers de visualización
function formatDate(dateStr: string): string {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleString('es-VE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

function getDifferenceColor(diff: number): string {
  if (diff > 0) return 'text-success';
  if (diff < 0) return 'text-error';
  return 'text-medium-emphasis';
}

function formatDifference(diff: number, prefix: string): string {
  const sign = diff > 0 ? '+' : '';
  const label = diff > 0 ? ' (Sobrante)' : diff < 0 ? ' (Faltante)' : ' (Cuadrado)';
  return `${sign}${prefix}${diff.toFixed(2)}${label}`;
}
</script>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
.italic { font-style: italic; }
</style>