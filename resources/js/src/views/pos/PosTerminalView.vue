<template>
  <div class="pos-mobile-viewport">
    <!-- ======================================================== -->
    <!-- NIVEL 1: CABECERA FIJA (~95px)                          -->
    <!-- ======================================================== -->
    <div class="pos-level-1 px-3 pt-2 pb-1 border-b bg-surface flex-shrink-0">
      <!-- Buscador y Chip de Cliente -->
      <div class="d-flex align-center gap-2 mb-2">
        <v-text-field
          ref="barcodeInputRef"
          v-model="searchQuery"
          placeholder="Buscar o escanear..."
          prepend-inner-icon="mdi-magnify"
          append-inner-icon="mdi-barcode-scan"
          variant="outlined"
          density="compact"
          hide-details
          clearable
          class="flex-grow-1"
          @keydown.enter.prevent="onBarcodeScanEnter"
          @update:model-value="onSearchInput"
          @click:clear="onSearchClear"
        />

        <!-- Chip del Cliente -->
        <v-chip
          size="small"
          :color="posStore.selectedCustomerId ? 'primary' : undefined"
          :variant="posStore.selectedCustomerId ? 'flat' : 'outlined'"
          class="font-weight-medium flex-shrink-0 customer-action-chip"
          @click="isCustomerDialogOpen = true"
        >
          <v-icon
            start
            size="16"
            :color="posStore.selectedCustomerId ? undefined : 'primary'"
          >
            mdi-account
          </v-icon>
          <span class="text-truncate max-w-customer">{{ currentCustomerLabel }}</span>
        </v-chip>
      </div>

      <!-- Filtro Horizontal de Categorías -->
      <PosCategoryChips
        :categories="categories"
        :selected-category="selectedCategory"
        @select="filterByCategory"
      />
    </div>

    <!-- ======================================================== -->
    <!-- NIVEL 2: VIRTUAL SCROLLER DEL CATÁLOGO                   -->
    <!-- ======================================================== -->
    <div class="pos-level-2 bg-surface">
      <PosProductVirtualList
        :products="products"
        :is-loading="isLoadingProducts"
      />
    </div>

    <!-- ======================================================== -->
    <!-- NIVEL 3: DOCK FIJO DE COBRO EN LA ZONA DEL PULGAR        -->
    <!-- ======================================================== -->
    <PosMobileDock
      class="pos-level-3 flex-shrink-0"
      @open-cart="isCartDrawerOpen = true"
    />

    <!-- ======================================================== -->
    <!-- DIÁLOGOS Y DRAWERS COMPLEMENTARIOS                      -->
    <!-- ======================================================== -->
    <PosCustomerDialog
      v-model="isCustomerDialogOpen"
      :customers="customers"
    />

    <PosCartDrawer
      v-model="isCartDrawerOpen"
      :customers="customers"
      @sale-completed="onSaleCompleted"
    />
  </div>
</template>

<script setup lang="ts">
/* ... Lógica de script intacta ... */
import { ref, computed, onMounted, nextTick } from 'vue';
import { usePosStore } from '@/stores/pos';
import { useNotificationStore } from '@/stores/notification';
import catalogService from '@/api/catalog.api';
import partiesService from '@/api/parties.api';
import settingsService from '@/api/settings.api';
import type { Product, Category } from '@/types/catalog';
import type { Customer } from '@/types/parties';

import PosCustomerDialog from './components/PosCustomerDialog.vue';
import PosCategoryChips from './components/PosCategoryChips.vue';
import PosProductVirtualList from './components/PosProductVirtualList.vue';
import PosMobileDock from './components/PosMobileDock.vue';
import PosCartDrawer from './components/PosCartDrawer.vue';

const posStore = usePosStore();
const notificationStore = useNotificationStore();

const barcodeInputRef = ref<HTMLInputElement | null>(null);

const products = ref<Product[]>([]);
const categories = ref<Category[]>([]);
const customers = ref<Customer[]>([]);

const isLoadingProducts = ref(false);
const searchQuery = ref('');
const selectedCategory = ref<string | null>(null);
const isCustomerDialogOpen = ref(false);
const isCartDrawerOpen = ref(false);
let searchDebounceTimeout: ReturnType<typeof setTimeout> | null = null;

const currentCustomerLabel = computed(() => {
  if (!posStore.selectedCustomerId) return 'C. Final';
  const c = customers.value.find(item => item.id === posStore.selectedCustomerId);
  return c ? c.name : 'Cliente';
});

async function loadInitialData() {
  try {
    const [fetchedCategories, fetchedCustomers, fetchedSettings] = await Promise.all([
      catalogService.getAllCategories(),
      partiesService.getAllCustomers(),
      settingsService.getSettings(),
    ]);

    categories.value = fetchedCategories;
    customers.value = fetchedCustomers;

    const rate = Number(fetchedSettings['exchange_rate'] || fetchedSettings['bcv_rate'] || 1.0);
    posStore.setExchangeRate(rate);
  } catch {
    notificationStore.warning('Advertencia al cargar configuración del POS.');
  }
}

async function loadProducts() {
  isLoadingProducts.value = true;
  try {
    const response = await catalogService.getProducts({
      page: 1,
      per_page: 50,
      search: searchQuery.value || undefined,
      category_id: selectedCategory.value || undefined,
      sort_field: 'name',
      sort_direction: 'asc',
    });

    products.value = response.data;
  } catch (error: any) {
    notificationStore.warning(error?.response?.data?.message || 'Error al cargar catálogo.');
  } finally {
    isLoadingProducts.value = false;
  }
}

function filterByCategory(categoryId: string | null) {
  selectedCategory.value = categoryId;
  loadProducts();
}

function onSearchInput() {
  if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
  searchDebounceTimeout = setTimeout(() => {
    loadProducts();
  }, 300);
}

function onSearchClear() {
  searchQuery.value = '';
  loadProducts();
}

async function onBarcodeScanEnter() {
  const code = searchQuery.value.trim();
  if (!code) return;

  const foundLocal = products.value.find(p => p.sku.toLowerCase() === code.toLowerCase());
  if (foundLocal) {
    posStore.addToCart(foundLocal);
    searchQuery.value = '';
    return;
  }

  try {
    const result = await catalogService.lookupBarcode(code);
    if (result.exists_locally && result.data?.id) {
      const fullProduct = await catalogService.getProductById(result.data.id);
      posStore.addToCart(fullProduct);
      searchQuery.value = '';
    } else {
      notificationStore.warning(`Código ${code} no encontrado.`);
    }
  } catch {
    notificationStore.warning('Error al escanear código.');
  }
}

function onSaleCompleted() {
  loadProducts();
  nextTick(() => {
    barcodeInputRef.value?.focus();
  });
}

onMounted(async () => {
  await Promise.all([loadInitialData(), loadProducts()]);
  nextTick(() => {
    barcodeInputRef.value?.focus();
  });
});
</script>

<style scoped>
/*
  Blindaje de alto:
  1. Ocupa el 100% de la altura disponible dentro de <v-main>.
  2. Fallback con calc y variables CSS nativas de Vuetify si el contenedor padre tuviera altura auto.
*/
.pos-mobile-viewport {
  width: 100%;
  height: 100%;
  max-height: calc(100dvh - var(--v-layout-top, 0px) - var(--v-layout-bottom, 0px));
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.pos-level-1 {
  user-select: none;
}

/*
  El Nivel 2 toma estrictamente todo el remanente disponible.
  flex: 1 1 0px y min-height: 0 obligan al virtual scroller a confinarse
  en este espacio sin empujar el dock hacia abajo.
*/
.pos-level-2 {
  flex: 1 1 0px;
  min-height: 0;
  overflow: hidden;
}

.pos-level-3 {
  z-index: 10;
}

.customer-chip {
  height: 38px !important;
  cursor: pointer;
}

.max-w-customer {
  max-width: 90px;
}

.gap-2 { gap: 8px; }
.customer-action-chip {
  height: 40px !important; /* Misma altura visual que el v-text-field compact */
  border-color: rgba(255, 255, 255, 0.22) !important;
  color: rgba(255, 255, 255, 0.9) !important;
}

.max-w-customer {
  max-width: 80px;
}
</style>