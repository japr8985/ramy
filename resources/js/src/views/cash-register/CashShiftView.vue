<template>
  <div class="cash-shift-view w-100">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Control y Arqueo de Caja</h1>
        <p class="text-caption text-medium-emphasis">
          Gestión de turnos de cobro, movimientos de efectivo y conciliación bimoneda (USD / VES).
        </p>
      </div>

      <div v-if="activeShiftData" class="d-flex gap-2 mt-3 mt-sm-0">
        <v-btn
          variant="tonal"
          color="secondary"
          size="small"
          prepend-icon="mdi-swap-horizontal"
          @click="openMovementDialog"
        >
          Movimiento Manual
        </v-btn>

        <v-btn
          color="error"
          size="small"
          prepend-icon="mdi-lock-check-outline"
          @click="openCloseDialog"
        >
          Cerrar Turno y Arqueo
        </v-btn>
      </div>
    </div>

    <!-- Indicador de Carga Inicial -->
    <div v-if="isLoading" class="py-12 text-center">
      <v-progress-circular indeterminate color="primary" size="36" />
      <div class="text-caption text-medium-emphasis mt-3">Verificando estado de sesión de caja...</div>
    </div>

    <!-- ESCENARIO A: No hay Turno Activo (Formulario de Apertura) -->
    <v-card v-else-if="!activeShiftData" elevation="0" border rounded="lg" class="pa-6 mx-auto" style="max-width: 600px;">
      <div class="text-center mb-6">
        <v-avatar color="primary" variant="tonal" size="56" class="mb-3">
          <v-icon size="32">mdi-cash-register</v-icon>
        </v-avatar>
        <h2 class="text-h6 font-weight-bold">Apertura de Turno de Caja</h2>
        <p class="text-caption text-medium-emphasis">
          Indique los fondos base disponibles en caja física antes de iniciar la facturación en el POS.
        </p>
      </div>

      <v-form ref="openFormRef" @submit.prevent="submitOpenShift">
        <v-row dense>
          <v-col cols="12" sm="6">
            <v-text-field
              v-model.number="openForm.initial_cash_usd"
              label="Fondo Inicial ($ Dólares)"
              type="number"
              min="0"
              step="0.01"
              variant="outlined"
              density="comfortable"
              prefix="$"
              placeholder="0.00"
              class="mb-2"
            />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field
              v-model.number="openForm.initial_cash_ves"
              label="Fondo Inicial (Bs. Bolívares)"
              type="number"
              min="0"
              step="0.01"
              variant="outlined"
              density="comfortable"
              prefix="Bs."
              placeholder="0.00"
              class="mb-2"
            />
          </v-col>

          <v-col cols="12">
            <v-textarea
              v-model="openForm.notes"
              label="Observaciones de Apertura"
              placeholder="Ej. Billetes de baja denominación aportados por gerencia..."
              variant="outlined"
              density="comfortable"
              rows="3"
              counter="500"
              class="mb-3"
            />
          </v-col>
        </v-row>

        <v-btn
          type="submit"
          color="primary"
          block
          size="large"
          prepend-icon="mdi-door-open"
          :loading="isSubmitting"
        >
          Iniciar Turno de Cobro
        </v-btn>
      </v-form>
    </v-card>

    <!-- ESCENARIO B: Turno de Caja Abierto -->
    <div v-else class="d-flex flex-column gap-6">
      <!-- Tarjeta Resumen de Estado del Turno -->
      <v-card elevation="0" border rounded="lg" class="pa-4 bg-surface-light">
        <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center gap-3">
          <div class="d-flex align-center gap-3">
            <v-avatar color="success" variant="tonal" size="44">
              <v-icon size="24">mdi-lock-open-outline</v-icon>
            </v-avatar>
            <div>
              <div class="d-flex align-center gap-2">
                <span class="text-subtitle-1 font-weight-bold">Turno Abierto</span>
                <v-chip color="success" size="x-small" variant="flat" class="font-weight-bold">EN VIVO</v-chip>
              </div>
              <div class="text-caption text-medium-emphasis">
                Iniciado el {{ formatDate(activeShiftData.shift.opened_at) }} por <strong>{{ activeShiftData.shift.user?.name || 'Cajero' }}</strong>
              </div>
            </div>
          </div>

          <v-btn
            variant="text"
            density="compact"
            icon="mdi-refresh"
            :loading="isRefreshing"
            title="Recargar arqueo en vivo"
            @click="refreshShiftData"
          />
        </div>
      </v-card>

      <!-- Métricas Consolidadas del Arqueo Bimoneda -->
      <v-row dense>
        <!-- Tarjeta USD ($) -->
        <v-col cols="12" md="6">
          <v-card elevation="0" border rounded="lg" class="pa-4 h-100">
            <div class="d-flex justify-space-between align-center mb-3">
              <div class="d-flex align-center gap-2">
                <v-icon color="primary">mdi-currency-usd</v-icon>
                <span class="text-subtitle-2 font-weight-bold">Flujo de Efectivo ($ Dólares)</span>
              </div>
              <v-chip size="x-small" color="primary" variant="tonal" class="font-weight-bold">USD</v-chip>
            </div>

            <div class="d-flex flex-column gap-2 text-caption">
              <div class="d-flex justify-space-between">
                <span class="text-medium-emphasis">Fondo Inicial Base:</span>
                <span class="font-mono font-weight-medium">${{ summaryTotals.initial_usd.toFixed(2) }}</span>
              </div>
              <div class="d-flex justify-space-between">
                <span class="text-medium-emphasis">Ventas en Efectivo (+):</span>
                <span class="font-mono text-success font-weight-bold">+${{ summaryTotals.cash_sales_usd.toFixed(2) }}</span>
              </div>
              <div class="d-flex justify-space-between">
                <span class="text-medium-emphasis">Ingresos Manuales (+):</span>
                <span class="font-mono text-info font-weight-medium">+${{ summaryTotals.manual_in_usd.toFixed(2) }}</span>
              </div>
              <div class="d-flex justify-space-between">
                <span class="text-medium-emphasis">Egresos / Gastos Menores (-):</span>
                <span class="font-mono text-error font-weight-medium">-${{ summaryTotals.manual_out_usd.toFixed(2) }}</span>
              </div>

              <v-divider class="my-1" />

              <div class="d-flex justify-space-between align-baseline pt-1">
                <span class="text-subtitle-2 font-weight-bold">Esperado en Gaveta:</span>
                <span class="text-h6 font-weight-bold font-mono text-primary">
                  ${{ summaryTotals.expected_usd.toFixed(2) }}
                </span>
              </div>
            </div>
          </v-card>
        </v-col>

        <!-- Tarjeta VES (Bs.) -->
        <v-col cols="12" md="6">
          <v-card elevation="0" border rounded="lg" class="pa-4 h-100">
            <div class="d-flex justify-space-between align-center mb-3">
              <div class="d-flex align-center gap-2">
                <v-icon color="secondary">mdi-cash-multiple</v-icon>
                <span class="text-subtitle-2 font-weight-bold">Flujo de Efectivo (Bs. Bolívares)</span>
              </div>
              <v-chip size="x-small" color="secondary" variant="tonal" class="font-weight-bold">VES</v-chip>
            </div>

            <div class="d-flex flex-column gap-2 text-caption">
              <div class="d-flex justify-space-between">
                <span class="text-medium-emphasis">Fondo Inicial Base:</span>
                <span class="font-mono font-weight-medium">Bs. {{ summaryTotals.initial_ves.toFixed(2) }}</span>
              </div>
              <div class="d-flex justify-space-between">
                <span class="text-medium-emphasis">Ventas en Efectivo (+):</span>
                <span class="font-mono text-success font-weight-bold">+Bs. {{ summaryTotals.cash_sales_ves.toFixed(2) }}</span>
              </div>
              <div class="d-flex justify-space-between">
                <span class="text-medium-emphasis">Ingresos Manuales (+):</span>
                <span class="font-mono text-info font-weight-medium">+Bs. {{ summaryTotals.manual_in_ves.toFixed(2) }}</span>
              </div>
              <div class="d-flex justify-space-between">
                <span class="text-medium-emphasis">Egresos / Gastos Menores (-):</span>
                <span class="font-mono text-error font-weight-medium">-Bs. {{ summaryTotals.manual_out_ves.toFixed(2) }}</span>
              </div>

              <v-divider class="my-1" />

              <div class="d-flex justify-space-between align-baseline pt-1">
                <span class="text-subtitle-2 font-weight-bold">Esperado en Gaveta:</span>
                <span class="text-h6 font-weight-bold font-mono text-secondary">
                  Bs. {{ summaryTotals.expected_ves.toFixed(2) }}
                </span>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Tabla de Movimientos Manuales del Turno -->
      <v-card elevation="0" border rounded="lg">
        <div class="pa-4 d-flex justify-space-between align-center border-b">
          <div>
            <h2 class="text-subtitle-1 font-weight-bold">Movimientos de Caja del Turno</h2>
            <span class="text-caption text-medium-emphasis">Auditoría de egresos menores e inyecciones de cambio.</span>
          </div>

          <v-btn
            size="small"
            variant="tonal"
            prepend-icon="mdi-plus"
            @click="openMovementDialog"
          >
            Nuevo Movimiento
          </v-btn>
        </div>

        <v-table density="comfortable" hover>
          <thead>
            <tr>
              <th>Hora</th>
              <th>Tipo</th>
              <th>Moneda</th>
              <th>Concepto / Motivo</th>
              <th class="text-right">Monto</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="movements.length === 0">
              <td colspan="5" class="text-center py-6 text-caption text-medium-emphasis">
                No se han registrado movimientos extraordinarios en este turno.
              </td>
            </tr>
            <tr v-for="movement in movements" :key="movement.id">
              <td class="text-caption font-mono">{{ formatTime(movement.created_at) }}</td>
              <td>
                <v-chip
                  size="x-small"
                  :color="movement.type === 'in' ? 'success' : 'error'"
                  variant="tonal"
                  class="font-weight-medium"
                >
                  {{ movement.type === 'in' ? 'Ingreso' : 'Egreso / Retiro' }}
                </v-chip>
              </td>
              <td class="font-mono text-caption">{{ movement.currency }}</td>
              <td class="text-body-2">
                {{ movement.concept }}
                <span v-if="movement.notes" class="text-caption text-medium-emphasis d-block font-italic">
                  {{ movement.notes }}
                </span>
              </td>
              <td class="text-right font-mono font-weight-bold" :class="movement.type === 'in' ? 'text-success' : 'text-error'">
                {{ movement.type === 'in' ? '+' : '-' }}{{ movement.currency === 'USD' ? '$' : 'Bs. ' }}{{ Number(movement.amount).toFixed(2) }}
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card>
    </div>

    <!-- DIÁLOGO: Registrar Movimiento Manual -->
    <v-dialog v-model="movementDialog" max-width="480" persistent>
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-4 d-flex align-center justify-space-between border-b">
          <span class="text-subtitle-1 font-weight-bold">Registrar Movimiento de Caja</span>
          <v-btn icon="mdi-close" variant="text" size="small" @click="movementDialog = false" />
        </v-card-title>

        <v-card-text class="pa-4">
          <v-form ref="movementFormRef" @submit.prevent="submitMovement">
            <v-row dense>
              <v-col cols="6">
                <v-select
                  v-model="movementForm.type"
                  :items="[
                    { title: 'Egreso / Retiro', value: 'out' },
                    { title: 'Ingreso / Entrada', value: 'in' },
                  ]"
                  label="Tipo *"
                  variant="outlined"
                  density="compact"
                />
              </v-col>

              <v-col cols="6">
                <v-select
                  v-model="movementForm.currency"
                  :items="['USD', 'VES']"
                  label="Moneda *"
                  variant="outlined"
                  density="compact"
                />
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model.number="movementForm.amount"
                  label="Monto *"
                  type="number"
                  min="0.01"
                  step="0.01"
                  variant="outlined"
                  density="compact"
                  :prefix="movementForm.currency === 'USD' ? '$' : 'Bs.'"
                  :rules="[(v: any) => v > 0 || 'El monto debe ser mayor a 0']"
                />
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="movementForm.concept"
                  label="Concepto / Razón *"
                  placeholder="Ej. Pago de flete, cambio en efectivo, almuerzo..."
                  variant="outlined"
                  density="compact"
                  :rules="[(v: string) => !!v.trim() || 'El concepto es obligatorio']"
                />
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="movementForm.notes"
                  label="Notas Adicionales"
                  variant="outlined"
                  density="compact"
                  rows="2"
                  counter="500"
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4 bg-surface-light">
          <v-spacer />
          <v-btn variant="text" size="small" :disabled="isSubmittingMovement" @click="movementDialog = false">
            Cancelar
          </v-btn>
          <v-btn
            color="primary"
            size="small"
            :loading="isSubmittingMovement"
            prepend-icon="mdi-content-save-outline"
            @click="submitMovement"
          >
            Guardar Movimiento
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DIÁLOGO: Arqueo y Cierre Definitivo de Turno -->
    <v-dialog v-model="closeDialog" max-width="520" persistent>
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-4 d-flex align-center justify-space-between border-b">
          <div class="d-flex align-center gap-2">
            <v-icon color="error" size="24">mdi-lock-alert-outline</v-icon>
            <span class="text-subtitle-1 font-weight-bold">Cierre y Arqueo de Caja</span>
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="closeDialog = false" />
        </v-card-title>

        <v-card-text class="pa-4">
          <p class="text-caption text-medium-emphasis mb-3">
            Realice el conteo físico de gaveta e ingrese los importes reales. El sistema calculará el sobrante o faltante.
          </p>

          <v-form ref="closeFormRef" @submit.prevent="submitCloseShift">
            <!-- Conteo Físico USD -->
            <v-card elevation="0" border rounded="lg" class="pa-3 mb-3 bg-surface-light">
              <div class="d-flex justify-space-between text-caption mb-1">
                <span class="font-weight-bold">Efectivo Físico en Dólares ($)</span>
                <span class="text-medium-emphasis font-mono">Esperado: ${{ summaryTotals.expected_usd.toFixed(2) }}</span>
              </div>
              <v-text-field
                v-model.number="closeForm.actual_cash_usd"
                placeholder="0.00"
                type="number"
                min="0"
                step="0.01"
                variant="outlined"
                density="compact"
                prefix="$"
                hide-details
              />
              <div
                v-if="closeForm.actual_cash_usd !== null"
                class="text-caption font-mono font-weight-bold mt-1 text-right"
                :class="calcDifferenceUsd >= 0 ? 'text-success' : 'text-error'"
              >
                Diferencia: {{ calcDifferenceUsd >= 0 ? '+' : '' }}${{ calcDifferenceUsd.toFixed(2) }}
                ({{ calcDifferenceUsd >= 0 ? 'Sobrante' : 'Faltante' }})
              </div>
            </v-card>

            <!-- Conteo Físico VES -->
            <v-card elevation="0" border rounded="lg" class="pa-3 mb-3 bg-surface-light">
              <div class="d-flex justify-space-between text-caption mb-1">
                <span class="font-weight-bold">Efectivo Físico en Bolívares (Bs.)</span>
                <span class="text-medium-emphasis font-mono">Esperado: Bs. {{ summaryTotals.expected_ves.toFixed(2) }}</span>
              </div>
              <v-text-field
                v-model.number="closeForm.actual_cash_ves"
                placeholder="0.00"
                type="number"
                min="0"
                step="0.01"
                variant="outlined"
                density="compact"
                prefix="Bs."
                hide-details
              />
              <div
                v-if="closeForm.actual_cash_ves !== null"
                class="text-caption font-mono font-weight-bold mt-1 text-right"
                :class="calcDifferenceVes >= 0 ? 'text-success' : 'text-error'"
              >
                Diferencia: {{ calcDifferenceVes >= 0 ? '+' : '' }}Bs. {{ calcDifferenceVes.toFixed(2) }}
                ({{ calcDifferenceVes >= 0 ? 'Sobrante' : 'Faltante' }})
              </div>
            </v-card>

            <v-textarea
              v-model="closeForm.notes"
              label="Observaciones del Cierre"
              placeholder="Justificación de discrepancias o detalles para administración..."
              variant="outlined"
              density="compact"
              rows="2"
              counter="500"
            />
          </v-form>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4 bg-surface-light">
          <v-spacer />
          <v-btn variant="text" size="small" :disabled="isSubmittingClose" @click="closeDialog = false">
            Cancelar
          </v-btn>
          <v-btn
            color="error"
            size="small"
            :loading="isSubmittingClose"
            prepend-icon="mdi-lock-check"
            @click="submitCloseShift"
          >
            Confirmar y Cerrar Caja
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import cashRegisterService from '@/api/cashRegister.api';
import { useNotificationStore } from '@/stores/notification';
import type {
  CashShiftActiveData,
  CashMovement,
  CashShiftSummaryTotals,
  CashMovementType,
  CashCurrency,
  OpenCashShiftPayload,
  RecordCashMovementPayload,
  CloseCashShiftPayload,
} from '@/types/cashRegister';

const notificationStore = useNotificationStore();

// Estados principales
const isLoading = ref(true);
const isRefreshing = ref(false);
const isSubmitting = ref(false);
const activeShiftData = ref<CashShiftActiveData | null>(null);

// Formulario de Apertura
const openFormRef = ref<HTMLFormElement | null>(null);
const openForm = reactive<OpenCashShiftPayload>({
  initial_cash_usd: 0,
  initial_cash_ves: 0,
  notes: '',
});

// Modal de Movimientos
const movementDialog = ref(false);
const isSubmittingMovement = ref(false);
const movementFormRef = ref<HTMLFormElement | null>(null);
const movementForm = reactive<{
  type: CashMovementType;
  currency: CashCurrency;
  amount: number | null;
  concept: string;
  notes: string;
}>({
  type: 'out',
  currency: 'USD',
  amount: null,
  concept: '',
  notes: '',
});

// Modal de Cierre
const closeDialog = ref(false);
const isSubmittingClose = ref(false);
const closeFormRef = ref<HTMLFormElement | null>(null);
const closeForm = reactive<{
  actual_cash_usd: number | null;
  actual_cash_ves: number | null;
  notes: string;
}>({
  actual_cash_usd: null,
  actual_cash_ves: null,
  notes: '',
});

// Cálculos reactivos de métricas
const summaryTotals = computed<CashShiftSummaryTotals>(() => {
  return activeShiftData.value?.summary || {
    initial_usd: 0,
    initial_ves: 0,
    cash_sales_usd: 0,
    cash_sales_ves: 0,
    manual_in_usd: 0,
    manual_in_ves: 0,
    manual_out_usd: 0,
    manual_out_ves: 0,
    expected_usd: 0,
    expected_ves: 0,
  };
});

const movements = computed<CashMovement[]>(() => {
  return activeShiftData.value?.shift.movements || [];
});

const calcDifferenceUsd = computed(() => {
  const actual = Number(closeForm.actual_cash_usd || 0);
  return actual - summaryTotals.value.expected_usd;
});

const calcDifferenceVes = computed(() => {
  const actual = Number(closeForm.actual_cash_ves || 0);
  return actual - summaryTotals.value.expected_ves;
});

// Carga del turno activo del usuario
async function loadActiveShift() {
  try {
    activeShiftData.value = await cashRegisterService.getCurrentShift();
  } catch {
    notificationStore.error('Error al consultar el estado de la caja.');
  } finally {
    isLoading.value = false;
  }
}

async function refreshShiftData() {
  if (!activeShiftData.value) return;
  isRefreshing.value = true;
  try {
    activeShiftData.value = await cashRegisterService.getShiftSummary(activeShiftData.value.shift.id);
  } catch {
    notificationStore.error('Error al refrescar el arqueo.');
  } finally {
    isRefreshing.value = false;
  }
}

// Envío de Apertura
async function submitOpenShift() {
  isSubmitting.value = true;
  try {
    await cashRegisterService.openShift({
      initial_cash_usd: Number(openForm.initial_cash_usd || 0),
      initial_cash_ves: Number(openForm.initial_cash_ves || 0),
      notes: openForm.notes?.trim() || null,
    });
    notificationStore.success('¡Turno de caja iniciado exitosamente!');
    await loadActiveShift();
  } catch (error: any) {
    notificationStore.error(error?.response?.data?.message || 'Error al abrir el turno.');
  } finally {
    isSubmitting.value = false;
  }
}

// Diálogo de Movimientos
function openMovementDialog() {
  movementForm.type = 'out';
  movementForm.currency = 'USD';
  movementForm.amount = null;
  movementForm.concept = '';
  movementForm.notes = '';
  movementDialog.value = true;
}

async function submitMovement() {
  if (!activeShiftData.value || !movementFormRef.value) return;

  const { valid } = await movementFormRef.value.validate();
  if (!valid) return;

  isSubmittingMovement.value = true;
  try {
    const payload: RecordCashMovementPayload = {
      type: movementForm.type,
      currency: movementForm.currency,
      amount: Number(movementForm.amount),
      concept: movementForm.concept.trim(),
      notes: movementForm.notes.trim() || null,
    };

    await cashRegisterService.recordMovement(activeShiftData.value.shift.id, payload);
    notificationStore.success('Movimiento de caja registrado.');
    movementDialog.value = false;
    await refreshShiftData();
  } catch (error: any) {
    notificationStore.error(error?.response?.data?.message || 'Error al registrar movimiento.');
  } finally {
    isSubmittingMovement.value = false;
  }
}

// Diálogo de Cierre
function openCloseDialog() {
  closeForm.actual_cash_usd = summaryTotals.value.expected_usd;
  closeForm.actual_cash_ves = summaryTotals.value.expected_ves;
  closeForm.notes = '';
  closeDialog.value = true;
}

async function submitCloseShift() {
  if (!activeShiftData.value) return;

  isSubmittingClose.value = true;
  try {
    const payload: CloseCashShiftPayload = {
      actual_cash_usd: Number(closeForm.actual_cash_usd || 0),
      actual_cash_ves: Number(closeForm.actual_cash_ves || 0),
      notes: closeForm.notes.trim() || null,
    };

    await cashRegisterService.closeShift(activeShiftData.value.shift.id, payload);
    notificationStore.success('Turno de caja cerrado y arqueado satisfactoriamente.');
    closeDialog.value = false;
    activeShiftData.value = null;
    await loadActiveShift();
  } catch (error: any) {
    notificationStore.error(error?.response?.data?.message || 'Error al cerrar el turno.');
  } finally {
    isSubmittingClose.value = false;
  }
}

// Helpers de fecha y hora
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

function formatTime(dateStr: string): string {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleTimeString('es-VE', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  });
}

onMounted(() => {
  loadActiveShift();
});
</script>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-6 { gap: 24px; }
.font-mono { font-family: monospace; }
</style>