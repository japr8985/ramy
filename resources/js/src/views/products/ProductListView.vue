<template>
  <div class="product-list-view w-100">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Inventario de Productos</h1>
        <p class="text-caption text-medium-emphasis">
          Supervisión de existencias, precios de venta y umbrales de reabastecimiento.
        </p>
      </div>

      <div class="d-flex align-center gap-2 mt-3 mt-sm-0">
        <v-btn
          color="primary"
          size="small"
          prepend-icon="mdi-plus"
          :to="{ name: 'products.create' }"
        >
          Nuevo Producto
        </v-btn>
      </div>
    </div>

    <!-- Fila de Tarjetas de KPIs de Inventario -->
    <v-row class="mb-4">
      <v-col cols="12" sm="4">
        <v-card elevation="0" border rounded="lg" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <span class="text-caption text-medium-emphasis text-uppercase font-weight-medium">
                Total Artículos
              </span>
              <h2 class="text-h5 font-weight-bold mt-1">
                {{ isKpisLoading ? '—' : kpis.total_products }}
              </h2>
            </div>
            <v-avatar color="primary" variant="tonal" rounded="lg" size="44">
              <v-icon size="24">mdi-package-variant-closed</v-icon>
            </v-avatar>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="4">
        <v-card elevation="0" border rounded="lg" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <span class="text-caption text-medium-emphasis text-uppercase font-weight-medium">
                Stock Crítico
              </span>
              <h2
                class="text-h5 font-weight-bold mt-1"
                :class="kpis.critical_stock > 0 ? 'text-error' : 'text-success'"
              >
                {{ isKpisLoading ? '—' : kpis.critical_stock }}
              </h2>
            </div>
            <v-avatar
              :color="kpis.critical_stock > 0 ? 'error' : 'success'"
              variant="tonal"
              rounded="lg"
              size="44"
            >
              <v-icon size="24">mdi-alert-box-outline</v-icon>
            </v-avatar>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="4">
        <v-card elevation="0" border rounded="lg" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <span class="text-caption text-medium-emphasis text-uppercase font-weight-medium">
                Valorización (USD)
              </span>
              <h2 class="text-h5 font-weight-bold mt-1">
                {{ isKpisLoading ? '—' : `$${Number(kpis.stock_value).toLocaleString('en-US', { minimumFractionDigits: 2 })}` }}
              </h2>
            </div>
            <v-avatar color="secondary" variant="tonal" rounded="lg" size="44">
              <v-icon size="24">mdi-cash-multiple</v-icon>
            </v-avatar>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Contenedor Principal: Filtros y Tabla -->
    <v-card elevation="0" border rounded="lg">
      <div class="pa-4 d-flex flex-column flex-md-row justify-space-between align-center gap-3">
        <!-- Barra de Búsqueda y Selector de Categoría -->
        <div class="d-flex flex-column flex-sm-row align-center gap-3 w-100" style="max-width: 640px;">
          <v-text-field
            v-model="searchQuery"
            placeholder="Buscar por nombre o código SKU..."
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
            v-model="selectedCategory"
            :items="categories"
            item-title="name"
            item-value="id"
            placeholder="Todas las categorías"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="w-100"
            style="max-width: 260px;"
            @update:model-value="onCategoryChange"
          />
        </div>

        <!-- Botón de Recargar -->
        <v-btn
          variant="text"
          density="compact"
          icon="mdi-refresh"
          :loading="isLoading"
          title="Recargar catálogo"
          @click="refreshData"
        />
      </div>

      <v-divider />

      <!-- Data Table Server -->
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        v-model:sort-by="sortBy"
        :headers="headers"
        :items="products"
        :items-length="totalProducts"
        :loading="isLoading"
        loading-text="Cargando productos..."
        no-data-text="No se encontraron productos en el inventario."
        density="comfortable"
        hover
        @update:options="loadProducts"
      >
        <!-- Columna: Producto (Nombre, SKU y Categoría) -->
        <template #item.name="{ item }">
          <div class="py-2">
            <div class="font-weight-medium text-body-2">{{ item.name }}</div>
            <div class="d-flex align-center gap-2 mt-1">
              <span class="text-caption text-medium-emphasis font-mono">
                {{ item.sku }}
              </span>
              <v-chip size="x-small" variant="flat" color="surface-variant">
                {{ item.category?.name || 'Sin Categoría' }}
              </v-chip>
            </div>
          </div>
        </template>

        <!-- Columna: Existencia / Stock -->
        <template #item.quantity="{ item }">
          <div class="d-flex align-center gap-2">
            <span class="font-weight-bold text-body-2">
              {{ item.quantity }} {{ item.unit?.symbol || '' }}
            </span>
            <v-chip
              v-if="item.quantity < item.min_stock"
              size="x-small"
              color="error"
              variant="tonal"
            >
              Mín: {{ item.min_stock }}
            </v-chip>
          </div>
        </template>

        <!-- Columna: Precio de Costo -->
        <template #item.purchase_price="{ item }">
          <span class="text-body-2 text-medium-emphasis">
            ${{ Number(item.purchase_price).toFixed(2) }}
          </span>
        </template>

        <!-- Columna: Precio de Venta (USD y VES) -->
        <template #item.selling_price="{ item }">
          <div>
            <span class="text-body-2 font-weight-bold">
              ${{ Number(item.selling_price).toFixed(2) }}
            </span>
            <div class="text-caption text-medium-emphasis">
              Bs. {{ (Number(item.selling_price) * exchangeRate).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
            </div>
          </div>
        </template>

        <!-- Columna: Estado (Activo en POS) -->
        <template #item.is_active="{ item }">
          <v-chip
            size="x-small"
            :color="item.is_active ? 'success' : 'surface-variant'"
            variant="tonal"
          >
            {{ item.is_active ? 'Activo' : 'Inactivo' }}
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
              :to="{ name: 'products.edit', params: { id: item.id } }"
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

    <!-- Diálogo Modal de Confirmación de Eliminación -->
    <v-dialog v-model="deleteDialog" max-width="440" persistent>
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-5 pb-3 d-flex align-center gap-2">
          <v-icon color="warning" size="24">mdi-alert-circle-outline</v-icon>
          <span class="text-subtitle-1 font-weight-bold">Eliminar Producto</span>
        </v-card-title>

        <v-card-text class="px-5 py-3">
          <p class="text-body-2 mb-2">
            ¿Está seguro de que desea remover el producto <strong>{{ targetProduct?.name }}</strong> del inventario?
          </p>
          <p class="text-caption text-medium-emphasis">
            Si el producto posee ventas históricas asociadas, el servidor bloqueará la acción para preservar la integridad contable.
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
import { ref, reactive, onMounted } from 'vue';
import catalogService from '@/api/catalog.api';
import settingsService from '@/api/settings.api';
import { useNotificationStore } from '@/stores/notification';
import type { Product, Category, ProductKpis } from '@/types/catalog';

const notificationStore = useNotificationStore();

// --- Estados de Datos y Filtros ---
const products = ref<Product[]>([]);
const totalProducts = ref(0);
const categories = ref<Category[]>([]);
const exchangeRate = ref(1.0);

const isLoading = ref(false);
const isKpisLoading = ref(false);
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref<Array<{ key: string; order: 'asc' | 'desc' }>>([{ key: 'created_at', order: 'desc' }]);

const searchQuery = ref('');
const selectedCategory = ref<string | null>(null);
let searchDebounceTimeout: ReturnType<typeof setTimeout> | null = null;

const kpis = reactive<ProductKpis>({
  total_products: 0,
  critical_stock: 0,
  stock_value: 0,
});

const headers = [
  { title: 'Producto / SKU', key: 'name', sortable: true },
  { title: 'Existencia', key: 'quantity', sortable: true },
  { title: 'Costo Unit.', key: 'purchase_price', sortable: true },
  { title: 'Precio Venta', key: 'selling_price', sortable: true },
  { title: 'Estado', key: 'is_active', sortable: false },
  { title: 'Acciones', key: 'actions', sortable: false, align: 'end' as const },
];

// --- Diálogo de Eliminación ---
const deleteDialog = ref(false);
const isDeleting = ref(false);
const targetProduct = ref<Product | null>(null);

// --- Funciones de Carga de Datos ---
async function loadKpis() {
  isKpisLoading.value = true;
  try {
    const data = await catalogService.getProductKpis();
    kpis.total_products = data.total_products;
    kpis.critical_stock = data.critical_stock;
    kpis.stock_value = data.stock_value;
  } catch {
    // Falla silenciosa de métricas para no bloquear la tabla
  } finally {
    isKpisLoading.value = false;
  }
}

async function loadInitialMetadata() {
  try {
    const [fetchedCategories, fetchedSettings] = await Promise.all([
      catalogService.getAllCategories(),
      settingsService.getSettings(),
    ]);
    categories.value = fetchedCategories;
    const rate = Number(fetchedSettings['exchange_rate'] || fetchedSettings['bcv_rate'] || 1.0);
    exchangeRate.value = rate > 0 ? rate : 1.0;
  } catch {
    // Si falla metadata no se detiene la ejecución general
  }
}

async function loadProducts() {
  isLoading.value = true;
  try {
    const sortField = sortBy.value.length ? sortBy.value[0].key : 'created_at';
    const sortDirection = sortBy.value.length ? sortBy.value[0].order : 'desc';

    const response = await catalogService.getProducts({
      page: page.value,
      per_page: itemsPerPage.value,
      search: searchQuery.value || undefined,
      category_id: selectedCategory.value || undefined,
      sort_field: sortField,
      sort_direction: sortDirection,
    });

    products.value = response.data;
    totalProducts.value = response.total;
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al cargar los productos del inventario.'
    );
  } finally {
    isLoading.value = false;
  }
}

function refreshData() {
  loadProducts();
  loadKpis();
}

function onSearchInput() {
  if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
  searchDebounceTimeout = setTimeout(() => {
    page.value = 1;
    loadProducts();
  }, 350);
}

function onSearchClear() {
  searchQuery.value = '';
  page.value = 1;
  loadProducts();
}

function onCategoryChange() {
  page.value = 1;
  loadProducts();
}

// --- Flujo de Eliminación Defensiva ---
function confirmDelete(product: Product) {
  targetProduct.value = product;
  deleteDialog.value = true;
}

function closeDeleteDialog() {
  deleteDialog.value = false;
  targetProduct.value = null;
}

async function executeDelete() {
  if (!targetProduct.value) return;

  isDeleting.value = true;
  try {
    await catalogService.deleteProduct(targetProduct.value.id);
    notificationStore.success('Producto eliminado del inventario con éxito.');
    closeDeleteDialog();

    if (products.value.length === 1 && page.value > 1) {
      page.value -= 1;
    }
    refreshData();
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'No fue posible eliminar el producto.'
    );
  } finally {
    isDeleting.value = false;
  }
}

onMounted(() => {
  loadInitialMetadata();
  loadKpis();
});
</script>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
</style>