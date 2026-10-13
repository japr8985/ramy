<template>
  <div class="purchases-view w-100">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Órdenes y Recepción de Compras</h1>
        <p class="text-caption text-medium-emphasis">
          Ingreso de mercancía al almacén, actualización de costos de catálogo y auditoría de compras.
        </p>
      </div>

      <div class="d-flex gap-2 mt-3 mt-sm-0">
        <v-btn
          variant="tonal"
          color="secondary"
          size="small"
          prepend-icon="mdi-domain"
          :to="{ name: 'purchasing.suppliers' }"
        >
          Proveedores
        </v-btn>

        <v-btn
          color="primary"
          size="small"
          prepend-icon="mdi-package-down"
          @click="openCreateModal"
        >
          Registrar Compra
        </v-btn>
      </div>
    </div>

    <!-- Contenedor Principal: Filtros y Tabla Servidora -->
    <v-card elevation="0" border rounded="lg">
      <div class="pa-4 d-flex flex-column flex-md-row justify-space-between align-center gap-3">
        <div class="d-flex flex-column flex-sm-row align-center gap-3 w-100" style="max-width: 600px;">
          <v-text-field
            v-model="searchQuery"
            placeholder="Buscar por nro. de factura o proveedor..."
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
            placeholder="Estado"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="w-100"
            style="max-width: 200px;"
            @update:model-value="onFilterChange"
          />
        </div>

        <v-btn
          variant="text"
          density="compact"
          icon="mdi-refresh"
          :loading="isLoading"
          title="Recargar compras"
          @click="loadPurchases"
        />
      </div>

      <v-divider />

      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        v-model:sort-by="sortBy"
        :headers="headers"
        :items="purchases"
        :items-length="totalPurchases"
        :loading="isLoading"
        loading-text="Cargando órdenes de compra..."
        no-data-text="No se encontraron registros de compras con los filtros actuales."
        density="comfortable"
        hover
        @update:options="loadPurchases"
      >
        <!-- Nro. de Factura / Control -->
        <template #item.invoice_number="{ item }">
          <div class="py-2">
            <span class="font-mono font-weight-bold text-primary">{{ item.invoice_number || 'S/N' }}</span>
            <div class="text-caption text-medium-emphasis">
              Emitida: {{ formatDate(item.purchase_date) }}
            </div>
          </div>
        </template>

        <!-- Proveedor -->
        <template #item.supplier="{ item }">
          <div class="py-2">
            <div class="font-weight-medium text-body-2">{{ item.supplier?.name || 'Proveedor general' }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ item.supplier?.contact_person || item.supplier?.phone || '—' }}
            </div>
          </div>
        </template>

        <!-- Total de la Compra -->
        <template #item.total="{ item }">
          <span class="font-mono font-weight-bold text-body-2">${{ Number(item.total).toFixed(2) }}</span>
        </template>

        <!-- Estado -->
        <template #item.status="{ item }">
          <v-chip
            size="x-small"
            :color="resolveStatusColor(item.status)"
            variant="tonal"
            class="font-weight-medium text-capitalize"
          >
            {{ resolveStatusLabel(item.status) }}
          </v-chip>
        </template>

        <!-- Usuario Receptor -->
        <template #item.creator="{ item }">
          <span class="text-caption text-medium-emphasis">{{ item.creator?.name || 'Sistema' }}</span>
        </template>

        <!-- Acciones -->
        <template #item.actions="{ item }">
          <v-btn
            icon="mdi-eye-outline"
            size="x-small"
            variant="text"
            color="primary"
            title="Ver detalle de la compra"
            @click="openDetails(item)"
          />
        </template>
      </v-data-table-server>
    </v-card>

    <!-- DIÁLOGO: Registrar e Ingresar Compra al Almacén -->
    <v-dialog v-model="createDialog" max-width="960" persistent scrollable>
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-4 d-flex align-center justify-space-between border-b">
          <div class="d-flex align-center gap-2">
            <v-icon color="primary" size="24">mdi-package-down</v-icon>
            <span class="text-subtitle-1 font-weight-bold">Recepción de Mercancía e Ingreso a Inventario</span>
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="createDialog = false" />
        </v-card-title>

        <v-card-text class="pa-4">
          <v-form ref="formRef" @submit.prevent="submitPurchase">
            <!-- Datos Generales de la Compra -->
            <v-row dense class="mb-2">
              <v-col cols="12" sm="5">
                <v-autocomplete
                  v-model="form.supplier_id"
                  :items="suppliersList"
                  item-title="name"
                  item-value="id"
                  label="Proveedor *"
                  placeholder="Seleccione el proveedor..."
                  variant="outlined"
                  density="compact"
                  :rules="[(v: string) => !!v || 'Debe seleccionar un proveedor']"
                  :loading="isLoadingSuppliers"
                />
              </v-col>

              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="form.invoice_number"
                  label="N° Factura / Control"
                  placeholder="Ej. FAC-00912"
                  variant="outlined"
                  density="compact"
                />
              </v-col>

              <v-col cols="12" sm="3">
                <v-text-field
                  v-model="form.purchase_date"
                  label="Fecha de Compra *"
                  type="date"
                  variant="outlined"
                  density="compact"
                  :rules="[(v: string) => !!v || 'Indique la fecha']"
                />
              </v-col>

              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="form.due_date"
                  label="Fecha Vencimiento (Crédito)"
                  type="date"
                  variant="outlined"
                  density="compact"
                />
              </v-col>

              <v-col cols="12" sm="8">
                <v-text-field
                  v-model="form.notes"
                  label="Notas u Observaciones"
                  placeholder="Detalles del despacho o términos de crédito..."
                  variant="outlined"
                  density="compact"
                />
              </v-col>
            </v-row>

            <v-divider class="my-3" />

            <!-- Selector para Agregar Productos al Carrito de Compra -->
            <div class="d-flex align-center gap-2 mb-3">
              <v-autocomplete
                v-model="selectedProductId"
                :items="catalogProducts"
                item-title="name"
                item-value="id"
                label="Buscar producto del catálogo para recepcionar..."
                placeholder="Escriba el nombre o código de barra..."
                variant="outlined"
                density="compact"
                hide-details
                class="flex-grow-1"
                :loading="isLoadingCatalog"
                no-data-text="No se encontraron productos en catálogo."
              >
                <template #item="{ props, item: prodItem }">
                  <v-list-item
                    v-bind="props"
                    :subtitle="`SKU: ${prodItem.raw.sku || 'N/A'} | Stock Actual: ${prodItem.raw.quantity} | Costo Anterior: $${Number(prodItem.raw.cost_price || 0).toFixed(2)}`"
                  />
                </template>
              </v-autocomplete>

              <v-btn
                color="secondary"
                variant="tonal"
                prepend-icon="mdi-plus"
                :disabled="!selectedProductId"
                @click="addProductToCart"
              >
                Agregar
              </v-btn>
            </div>

            <!-- Tabla de Renglones de la Compra -->
            <v-card elevation="0" border rounded="lg" class="mb-2">
              <v-table density="compact">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th style="width: 110px;">Cantidad</th>
                    <th style="width: 140px;">Costo Unit. ($)</th>
                    <th style="width: 140px;">P. Venta Sug. ($)</th>
                    <th class="text-right" style="width: 130px;">Subtotal ($)</th>
                    <th style="width: 50px;"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="cartItems.length === 0">
                    <td colspan="6" class="text-center py-6 text-caption text-medium-emphasis">
                      Aún no ha agregado productos a esta recepción de compra.
                    </td>
                  </tr>
                  <tr v-for="(item, index) in cartItems" :key="item.product_id">
                    <td>
                      <div class="font-weight-medium text-body-2">{{ item.product_name }}</div>
                      <div class="text-caption text-medium-emphasis font-mono">
                        {{ item.sku ? `SKU: ${item.sku}` : '' }}
                      </div>
                    </td>
                    <td>
                      <v-text-field
                        v-model.number="item.quantity"
                        type="number"
                        min="1"
                        step="1"
                        density="compact"
                        variant="outlined"
                        hide-details
                        class="table-input"
                      />
                    </td>
                    <td>
                      <v-text-field
                        v-model.number="item.unit_price"
                        type="number"
                        min="0"
                        step="0.01"
                        prefix="$"
                        density="compact"
                        variant="outlined"
                        hide-details
                        class="table-input"
                      />
                    </td>
                    <td>
                      <v-text-field
                        v-model.number="item.selling_price"
                        type="number"
                        min="0"
                        step="0.01"
                        prefix="$"
                        placeholder="Opcional"
                        density="compact"
                        variant="outlined"
                        hide-details
                        class="table-input"
                      />
                    </td>
                    <td class="text-right font-mono font-weight-bold">
                      ${{ (item.quantity * item.unit_price).toFixed(2) }}
                    </td>
                    <td class="text-center">
                      <v-btn
                        icon="mdi-trash-can-outline"
                        size="x-small"
                        variant="text"
                        color="error"
                        @click="removeCartItem(index)"
                      />
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </v-card>

            <!-- Resumen Totalizador -->
            <div class="d-flex justify-end align-center gap-3 pt-2">
              <span class="text-subtitle-2 text-medium-emphasis">Total de la Factura:</span>
              <span class="text-h6 font-weight-bold font-mono text-primary">
                ${{ grandTotal.toFixed(2) }}
              </span>
            </div>
          </v-form>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4 bg-surface-light">
          <v-spacer />
          <v-btn variant="text" size="small" :disabled="isSubmitting" @click="createDialog = false">
            Cancelar
          </v-btn>
          <v-btn
            color="primary"
            size="small"
            :loading="isSubmitting"
            :disabled="cartItems.length === 0"
            prepend-icon="mdi-check-all"
            @click="submitPurchase"
          >
            Recepcionar y Sumar a Inventario
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DRAWER: Detalle Completo de la Compra -->
    <v-navigation-drawer v-model="drawer" location="right" temporary width="500">
      <div v-if="selectedPurchase" class="h-100 d-flex flex-column">
        <div class="pa-4 border-b d-flex justify-space-between align-center bg-surface-light">
          <div>
            <div class="text-caption text-medium-emphasis">Orden de Compra</div>
            <div class="text-subtitle-1 font-weight-bold font-mono">
              {{ selectedPurchase.invoice_number || 'Sin correlativo' }}
            </div>
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="drawer = false" />
        </div>

        <div class="flex-grow-1 overflow-y-auto pa-4">
          <!-- Metadatos de la Compra -->
          <div class="text-caption mb-3">
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Proveedor:</span>
              <span class="font-weight-medium">{{ selectedPurchase.supplier?.name || '—' }}</span>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Estado:</span>
              <v-chip size="x-small" :color="resolveStatusColor(selectedPurchase.status)" variant="tonal">
                {{ resolveStatusLabel(selectedPurchase.status) }}
              </v-chip>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Fecha de Compra:</span>
              <span>{{ formatDate(selectedPurchase.purchase_date) }}</span>
            </div>
            <div v-if="selectedPurchase.due_date" class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Fecha de Vencimiento:</span>
              <span>{{ formatDate(selectedPurchase.due_date) }}</span>
            </div>
            <div class="d-flex justify-space-between py-1">
              <span class="text-medium-emphasis">Registrado por:</span>
              <span>{{ selectedPurchase.creator?.name || 'Sistema' }}</span>
            </div>
          </div>

          <v-divider class="my-3" />

          <!-- Total de Compra -->
          <v-card elevation="0" border rounded="lg" class="pa-3 mb-3 bg-surface-light d-flex justify-space-between align-center">
            <span class="text-caption font-weight-bold text-medium-emphasis">Total Liquidado</span>
            <span class="font-mono text-h6 font-weight-bold text-primary">
              ${{ Number(selectedPurchase.total).toFixed(2) }}
            </span>
          </v-card>

          <!-- Renglones Recepcionados -->
          <span class="text-caption font-weight-bold text-uppercase d-block mb-2 text-medium-emphasis">
            Renglones Recepcionados
          </span>

          <div v-if="isLoadingDetails" class="py-4 text-center">
            <v-progress-circular indeterminate size="24" color="primary" />
          </div>

          <div v-else-if="!selectedPurchase.items || selectedPurchase.items.length === 0" class="pa-4 text-center text-caption text-medium-emphasis border rounded-lg bg-surface-light">
            No se encontraron ítems detallados para esta compra.
          </div>

          <div v-else class="d-flex flex-column gap-2 mb-3">
            <div
              v-for="item in selectedPurchase.items"
              :key="item.id"
              class="pa-3 border rounded-lg bg-surface-light d-flex justify-space-between align-center"
            >
              <div>
                <div class="text-body-2 font-weight-medium">{{ item.product?.name || 'Producto' }}</div>
                <div class="text-caption text-medium-emphasis font-mono">
                  {{ item.quantity }} unid. &times; ${{ Number(item.unit_price).toFixed(2) }}
                </div>
              </div>
              <div class="text-right font-mono font-weight-bold">
                ${{ Number(item.subtotal).toFixed(2) }}
              </div>
            </div>
          </div>

          <!-- Observaciones -->
          <div v-if="selectedPurchase.notes" class="pa-3 border rounded-lg bg-surface mt-3">
            <span class="text-caption font-weight-bold d-block text-medium-emphasis mb-1">Notas:</span>
            <p class="text-caption mb-0">{{ selectedPurchase.notes }}</p>
          </div>
        </div>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import purchasingService from '@/api/purchasing.api';
import catalogService from '@/api/catalog.api';
import { useNotificationStore } from '@/stores/notification';
import type {
  Purchase,
  PurchaseStatus,
  Supplier,
  ProcessPurchasePayload,
  ProcessPurchaseItemPayload,
} from '@/types/purchasing';
import type { Product } from '@/types/catalog';

const notificationStore = useNotificationStore();

// Estados de la tabla
const isLoading = ref(false);
const purchases = ref<Purchase[]>([]);
const totalPurchases = ref(0);
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref<Array<{ key: string; order: 'asc' | 'desc' }>>([{ key: 'purchase_date', order: 'desc' }]);
const searchQuery = ref('');
const selectedStatus = ref<PurchaseStatus | null>(null);
let searchDebounce: ReturnType<typeof setTimeout> | null = null;

const statusOptions = [
  { label: 'Todos los estados', value: null },
  { label: 'Recibida', value: 'received' },
  { label: 'Borrador', value: 'draft' },
  { label: 'Cancelada', value: 'cancelled' },
];

const headers = [
  { title: 'N° Factura / Emisión', key: 'invoice_number', sortable: true },
  { title: 'Proveedor', key: 'supplier', sortable: false },
  { title: 'Total ($)', key: 'total', sortable: true },
  { title: 'Estado', key: 'status', sortable: false },
  { title: 'Receptor', key: 'creator', sortable: false },
  { title: '', key: 'actions', sortable: false, align: 'end' as const },
];

// Diálogo de Registro
const createDialog = ref(false);
const isSubmitting = ref(false);
const formRef = ref<HTMLFormElement | null>(null);

const suppliersList = ref<Supplier[]>([]);
const isLoadingSuppliers = ref(false);

const catalogProducts = ref<Product[]>([]);
const isLoadingCatalog = ref(false);
const selectedProductId = ref<string | null>(null);

interface CartItem extends ProcessPurchaseItemPayload {
  product_name: string;
  sku: string | null;
}

const cartItems = ref<CartItem[]>([]);

const form = reactive<{
  supplier_id: string;
  purchase_date: string;
  due_date: string;
  invoice_number: string;
  notes: string;
}>({
  supplier_id: '',
  purchase_date: new Date().toISOString().substring(0, 10),
  due_date: '',
  invoice_number: '',
  notes: '',
});

// Drawer de Detalle
const drawer = ref(false);
const selectedPurchase = ref<Purchase | null>(null);
const isLoadingDetails = ref(false);

// Cálculos
const grandTotal = computed(() => {
  return cartItems.value.reduce((acc, item) => acc + item.quantity * item.unit_price, 0);
});

async function loadPurchases() {
  isLoading.value = true;
  try {
    const sortField = sortBy.value.length ? sortBy.value[0].key : 'purchase_date';
    const sortDirection = sortBy.value.length ? sortBy.value[0].order : 'desc';

    const response = await purchasingService.getPurchases({
      page: page.value,
      per_page: itemsPerPage.value,
      search: searchQuery.value || undefined,
      status: selectedStatus.value || undefined,
      sort_field: sortField,
      sort_direction: sortDirection,
    });

    purchases.value = response.data;
    totalPurchases.value = response.total;
  } catch (error: any) {
    notificationStore.error(error?.response?.data?.message || 'Error al obtener compras.');
  } finally {
    isLoading.value = false;
  }
}

function onSearchInput() {
  if (searchDebounce) clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    page.value = 1;
    loadPurchases();
  }, 350);
}

function onSearchClear() {
  searchQuery.value = '';
  page.value = 1;
  loadPurchases();
}

function onFilterChange() {
  page.value = 1;
  loadPurchases();
}

// Apertura y Carga del Modal de Nueva Compra
async function openCreateModal() {
  form.supplier_id = '';
  form.purchase_date = new Date().toISOString().substring(0, 10);
  form.due_date = '';
  form.invoice_number = '';
  form.notes = '';
  cartItems.value = [];
  selectedProductId.value = null;
  createDialog.value = true;

  await Promise.all([loadSuppliersList(), loadCatalogProducts()]);
}

async function loadSuppliersList() {
  if (suppliersList.value.length > 0) return;
  isLoadingSuppliers.value = true;
  try {
    suppliersList.value = await purchasingService.getAllSuppliers();
  } catch {
    notificationStore.show('Error al precargar proveedores.', 'warning');
  } finally {
    isLoadingSuppliers.value = false;
  }
}

async function loadCatalogProducts() {
  if (catalogProducts.value.length > 0) return;
  isLoadingCatalog.value = true;
  try {
    const response = await catalogService.getProducts({ per_page: 250 });
    catalogProducts.value = response.data;
  } catch {
    notificationStore.show('Error al precargar productos.', 'warning');
  } finally {
    isLoadingCatalog.value = false;
  }
}

function addProductToCart() {
  if (!selectedProductId.value) return;

  const product = catalogProducts.value.find((p) => p.id === selectedProductId.value);
  if (!product) return;

  const existing = cartItems.value.find((item) => item.product_id === product.id);
  if (existing) {
    existing.quantity += 1;
  } else {
    cartItems.value.push({
      product_id: product.id,
      product_name: product.name,
      sku: product.sku || null,
      quantity: 1,
      unit_price: Number(product.cost_price || 0),
      selling_price: Number(product.selling_price || 0) || null,
    });
  }

  selectedProductId.value = null;
}

function removeCartItem(index: number) {
  cartItems.value.splice(index, 1);
}

async function submitPurchase() {
  if (!formRef.value) return;

  const { valid } = await formRef.value.validate();
  if (!valid) return;

  if (cartItems.value.length === 0) {
    notificationStore.show('Debe agregar al menos un producto a la compra.', 'error');
    return;
  }

  isSubmitting.value = true;
  try {
    const payload: ProcessPurchasePayload = {
      supplier_id: form.supplier_id,
      purchase_date: form.purchase_date,
      due_date: form.due_date || null,
      invoice_number: form.invoice_number.trim() || null,
      notes: form.notes.trim() || null,
      items: cartItems.value.map((item) => ({
        product_id: item.product_id,
        quantity: Number(item.quantity),
        unit_price: Number(item.unit_price),
        selling_price: item.selling_price ? Number(item.selling_price) : null,
      })),
    };

    await purchasingService.receivePurchase(payload);
    notificationStore.show('¡Compra registrada e inventario actualizado exitosamente!', 'success');
    createDialog.value = false;
    await loadPurchases();
  } catch (error: any) {
    notificationStore.show(error?.response?.data?.message || 'Error al recepcionar la compra.', 'error');
  } finally {
    isSubmitting.value = false;
  }
}

// Drawer de Detalle
async function openDetails(purchase: Purchase) {
  selectedPurchase.value = purchase;
  drawer.value = true;

  if (!purchase.items || purchase.items.length === 0) {
    isLoadingDetails.value = true;
    try {
      selectedPurchase.value = await purchasingService.getPurchaseById(purchase.id);
    } catch {
      notificationStore.show('Error al consultar renglones de la compra.', 'warning');
    } finally {
      isLoadingDetails.value = false;
    }
  }
}

// Helpers
function resolveStatusColor(status: PurchaseStatus): string {
  switch (status) {
    case 'received':
      return 'success';
    case 'draft':
      return 'warning';
    case 'cancelled':
      return 'error';
    default:
      return 'surface-variant';
  }
}

function resolveStatusLabel(status: PurchaseStatus): string {
  switch (status) {
    case 'received':
      return 'Recibida';
    case 'draft':
      return 'Borrador';
    case 'cancelled':
      return 'Cancelada';
    default:
      return status;
  }
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
  loadPurchases();
});
</script>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
.table-input {
  max-width: 120px;
}
</style>