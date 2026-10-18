<template>
  <div class="product-form-view w-100">
    <!-- Encabezado de la página -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div class="d-flex align-center gap-3">
        <v-btn
          icon="mdi-arrow-left"
          variant="text"
          density="comfortable"
          :to="{ name: 'products.index' }"
          title="Regresar al inventario"
        />
        <div>
          <h1 class="text-h5 font-weight-bold">
            {{ isEditing ? 'Editar Producto' : 'Nuevo Producto' }}
          </h1>
          <p class="text-caption text-medium-emphasis">
            {{ isEditing ? 'Actualice las especificaciones técnicas, precios o existencias.' : 'Registre un nuevo artículo en el catálogo comercial.' }}
          </p>
        </div>
      </div>

      <div class="d-flex align-center gap-2 mt-3 mt-sm-0">
        <v-btn
          variant="tonal"
          color="secondary"
          size="small"
          :to="{ name: 'products.index' }"
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
          {{ isEditing ? 'Actualizar Producto' : 'Guardar Producto' }}
        </v-btn>
      </div>
    </div>

    <!-- Skeleton durante carga inicial -->
    <v-row v-if="isLoading">
      <v-col cols="12" md="8">
        <v-skeleton-loader type="article, actions" class="border rounded-lg mb-4" />
        <v-skeleton-loader type="paragraph" class="border rounded-lg" />
      </v-col>
      <v-col cols="12" md="4">
        <v-skeleton-loader type="card" class="border rounded-lg" />
      </v-col>
    </v-row>

    <!-- Formulario Principal -->
    <v-form v-else ref="formRef" @submit.prevent="submit">
      <v-row>
        <!-- Columna Izquierda: Información Básica e Inventario -->
        <v-col cols="12" md="8">
          <!-- Bloque Identificación -->
          <v-card elevation="0" border rounded="lg" class="pa-5 mb-5">
            <h2 class="text-subtitle-1 font-weight-bold mb-1">Identificación y Clasificación</h2>
            <span class="text-caption text-medium-emphasis d-block mb-4">Código único, denominación comercial y categorización.</span>

            <v-row>
              <!-- Campo Código de Barras / SKU con Escáner y Consulta -->
              <v-col cols="12" sm="7">
                <v-text-field
                  ref="skuInputRef"
                  v-model="form.sku"
                  label="Código de Barras / SKU *"
                  placeholder="Escanee o ingrese código..."
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-barcode-scan"
                  :rules="[rules.required, rules.maxLength(50)]"
                  class="mb-2"
                  @click:prepend-inner="isScannerOpen = true"
                  @keydown.enter.stop.prevent="resolveBarcode"
                >
                  <template #append-inner>
                    <div class="d-flex align-center gap-1">
                      <!-- Botón para activar cámara -->
                      <v-btn
                        icon="mdi-camera-outline"
                        size="x-small"
                        variant="text"
                        color="primary"
                        title="Escanear con la cámara del dispositivo"
                        @click="isScannerOpen = true"
                      />

                      <!-- Botón de consulta manual/online -->
                      <v-btn
                        v-if="form.sku"
                        icon="mdi-cloud-search-outline"
                        size="x-small"
                        variant="text"
                        color="primary"
                        :loading="isLookingUpBarcode"
                        title="Consultar en base de datos e internet"
                        @click="resolveBarcode"
                      />
                    </div>
                  </template>
                </v-text-field>
              </v-col>

              <v-col cols="12" sm="5">
                <v-switch
                  v-model="form.is_active"
                  color="success"
                  label="Producto Activo en POS"
                  density="compact"
                  inset
                  hide-details
                  class="pt-1"
                />
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="form.name"
                  label="Nombre del Producto *"
                  placeholder="Ej. Harina de Maíz Precocida 1kg"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-package-variant-closed"
                  :rules="[rules.required, rules.maxLength(150)]"
                  class="mb-2"
                />
              </v-col>

              <v-col cols="12" sm="6">
                <v-select
                  v-model="form.category_id"
                  :items="categories"
                  item-title="name"
                  item-value="id"
                  label="Categoría *"
                  placeholder="Seleccione categoría"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-tag-outline"
                  :rules="[rules.required]"
                  class="mb-2"
                />
              </v-col>

              <v-col cols="12" sm="6">
                <v-select
                  v-model="form.unit_id"
                  :items="units"
                  :item-title="(item: any) => `${item.name} (${item.symbol})`"
                  item-value="id"
                  label="Unidad de Medida *"
                  placeholder="Seleccione unidad"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-scale"
                  :rules="[rules.required]"
                  class="mb-2"
                />
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Descripción Comercial"
                  placeholder="Detalles sobre marca, presentación o empaque..."
                  variant="outlined"
                  density="comfortable"
                  rows="2"
                  counter="500"
                  :rules="[rules.maxLength(500)]"
                  class="mb-2"
                />
              </v-col>
            </v-row>
          </v-card>

          <!-- Bloque Stock y Existencias -->
          <v-card elevation="0" border rounded="lg" class="pa-5">
            <h2 class="text-subtitle-1 font-weight-bold mb-1">Control de Inventario</h2>
            <span class="text-caption text-medium-emphasis d-block mb-4">Parámetros de existencias y umbrales de reabastecimiento.</span>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="form.quantity"
                  label="Stock Inicial / Existencia Actual *"
                  type="number"
                  min="0"
                  step="1"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-archive-outline"
                  :rules="[rules.required, rules.positiveInteger]"
                />
              </v-col>

              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="form.min_stock"
                  label="Stock Mínimo de Alerta *"
                  type="number"
                  min="0"
                  step="1"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-alert-box-outline"
                  hint="Generará alerta visual en el panel si el stock baja de esta cifra."
                  persistent-hint
                  :rules="[rules.required, rules.positiveInteger]"
                />
              </v-col>

              <v-col cols="12" class="mt-2">
                <v-textarea
                  v-model="form.notes"
                  label="Notas Internas de Inventario (Opcional)"
                  placeholder="Ubicación en almacén, lote preferido o notas de proveedor..."
                  variant="outlined"
                  density="comfortable"
                  rows="2"
                  counter="500"
                  :rules="[rules.maxLength(500)]"
                />
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <!-- Columna Derecha: Precios y Moneda Dual (USD & VES) -->
        <v-col cols="12" md="4">
          <v-card elevation="0" border rounded="lg" class="pa-5 mb-5">
            <div class="d-flex align-center justify-space-between mb-2">
              <h2 class="text-subtitle-1 font-weight-bold">Estructura de Precios</h2>
              <v-chip size="x-small" color="primary" variant="tonal" class="font-mono">
                Tasa: {{ exchangeRate.toFixed(2) }} VES
              </v-chip>
            </div>
            <span class="text-caption text-medium-emphasis d-block mb-4">
              Base fijada en USD con equivalencia automática al cambio oficial.
            </span>

            <!-- Precio de Costo -->
            <v-text-field
              v-model.number="form.purchase_price"
              label="Precio de Costo (USD) *"
              type="number"
              step="0.01"
              min="0"
              prefix="$"
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-tag-minus-outline"
              :rules="[rules.required, rules.positiveDecimal]"
              class="mb-1"
            />
            <div class="text-caption text-medium-emphasis mb-4 text-right font-mono">
              Equivalente: <strong>Bs. {{ costPriceVes }}</strong>
            </div>

            <!-- Precio de Venta -->
            <v-text-field
              v-model.number="form.selling_price"
              label="Precio de Venta (USD) *"
              type="number"
              step="0.01"
              min="0"
              prefix="$"
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-tag-plus-outline"
              :rules="[rules.required, rules.positiveDecimal]"
              class="mb-1"
            />
            <div class="text-caption text-medium-emphasis mb-4 text-right font-mono">
              Equivalente: <strong>Bs. {{ sellingPriceVes }}</strong>
            </div>

            <v-divider class="my-3" />

            <!-- Indicadores de Rentabilidad en Tiempo Real -->
            <div class="d-flex justify-space-between align-center mb-1">
              <span class="text-caption text-medium-emphasis">Margen Bruto Unitario:</span>
              <span class="text-caption font-weight-bold font-mono" :class="profitMargin >= 0 ? 'text-success' : 'text-error'">
                ${{ unitProfit.toFixed(2) }} ({{ profitMargin.toFixed(1) }}%)
              </span>
            </div>

            <v-alert
              v-if="unitProfit < 0"
              type="error"
              variant="tonal"
              density="compact"
              rounded="lg"
              class="text-caption mt-2"
            >
              El precio de venta es inferior al costo.
            </v-alert>
          </v-card>
        </v-col>
      </v-row>
    </v-form>

    <!-- Modal de Escáner por Cámara -->
    <PosBarcodeScannerDialog
      v-model="isScannerOpen"
      @detected="handleBarcodeDetected"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import catalogService from '@/api/catalog.api';
import settingsService from '@/api/settings.api';
import { useNotificationStore } from '@/stores/notification';
import type { Category, Unit, CreateProductPayload } from '@/types/catalog';
import PosBarcodeScannerDialog from '@/views/pos/components/PosBarcodeScannerDialog.vue';

const route = useRoute();
const router = useRouter();
const notificationStore = useNotificationStore();

const productId = computed(() => route.params.id as string | undefined);
const isEditing = computed(() => !!productId.value);

const isLoading = ref(true);
const isSaving = ref(false);
const isLookingUpBarcode = ref(false);
const isScannerOpen = ref(false);

const formRef = ref<HTMLFormElement | null>(null);
const skuInputRef = ref<any>(null);

// Datos relacionales y de configuración
const categories = ref<Category[]>([]);
const units = ref<Unit[]>([]);
const exchangeRate = ref<number>(1.0);

// Estado reactivo del formulario
const form = reactive<CreateProductPayload>({
  sku: '',
  name: '',
  category_id: '',
  unit_id: '',
  purchase_price: 0,
  selling_price: 0,
  quantity: 0,
  min_stock: 5,
  is_active: true,
  description: '',
  notes: '',
});

// Reglas de validación
const rules = {
  required: (v: unknown) => (v !== null && v !== undefined && v !== '') || 'Este campo es obligatorio.',
  maxLength: (max: number) => (v: string | null | undefined) =>
    !v || v.length <= max || `Máximo ${max} caracteres permitidos.`,
  positiveInteger: (v: number | string) => {
    const num = Number(v);
    return (!isNaN(num) && num >= 0 && Number.isInteger(num)) || 'Debe ser un número entero mayor o igual a 0.';
  },
  positiveDecimal: (v: number | string) => {
    const num = Number(v);
    return (!isNaN(num) && num >= 0) || 'Debe ser un valor numérico válido.';
  },
};

// Conversiones dinámicas en VES y Rentabilidad
const costPriceVes = computed(() => {
  const price = Number(form.purchase_price) || 0;
  return (price * exchangeRate.value).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});

const sellingPriceVes = computed(() => {
  const price = Number(form.selling_price) || 0;
  return (price * exchangeRate.value).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});

const unitProfit = computed(() => {
  const selling = Number(form.selling_price) || 0;
  const cost = Number(form.purchase_price) || 0;
  return selling - cost;
});

const profitMargin = computed(() => {
  const selling = Number(form.selling_price) || 0;
  const cost = Number(form.purchase_price) || 0;
  if (selling <= 0) return 0;
  return ((selling - cost) / selling) * 100;
});

/**
 * Carga inicial de datos maestros y producto
 */
async function loadInitialData() {
  isLoading.value = true;
  try {
    const [fetchedCategories, fetchedUnits, fetchedSettings] = await Promise.all([
      catalogService.getAllCategories(),
      catalogService.getUnits(),
      settingsService.getSettings(),
    ]);

    categories.value = fetchedCategories;
    units.value = fetchedUnits;

    const rate = Number(fetchedSettings['exchange_rate'] || fetchedSettings['bcv_rate'] || 1.0);
    exchangeRate.value = rate > 0 ? rate : 1.0;

    if (isEditing.value && productId.value) {
      const product = await catalogService.getProductById(productId.value);
      form.sku = product.sku;
      form.name = product.name;
      form.category_id = product.category_id;
      form.unit_id = product.unit_id;
      form.purchase_price = Number(product.purchase_price);
      form.selling_price = Number(product.selling_price);
      form.quantity = Number(product.quantity);
      form.min_stock = Number(product.min_stock);
      form.is_active = Boolean(product.is_active);
      form.description = product.description || '';
      form.notes = product.notes || '';
    } else {
      if (categories.value.length > 0 && !form.category_id) {
        form.category_id = categories.value[0].id;
      }
      if (units.value.length > 0 && !form.unit_id) {
        form.unit_id = units.value[0].id;
      }
    }
  } catch (error: any) {
    notificationStore.error(error?.response?.data?.message || 'Error al cargar los datos del formulario.');
    router.push({ name: 'products.index' });
  } finally {
    isLoading.value = false;
  }
}

/**
 * Handler invocado al detectar código desde la cámara
 */
async function handleBarcodeDetected(scannedCode: string) {
  const cleanCode = scannedCode.trim();
  if (!cleanCode) return;

  form.sku = cleanCode;
  await resolveBarcode();
}

/**
 * Consulta de código de barras asistida (local + internet)
 */
async function resolveBarcode() {
  const code = form.sku ? form.sku.trim() : '';
  if (!code || isLookingUpBarcode.value) return;

  isLookingUpBarcode.value = true;
  try {
    const result = await catalogService.lookupBarcode(code);

    const isLocal = result.source === 'local' || (result as any).exists_locally;

    if (isLocal && result.data) {
      if (!isEditing.value) {
        notificationStore.info(`El código ya existe en inventario local: "${result.data.name}".`);
      } else {
        notificationStore.info('Datos vinculados a inventario local.');
      }
      form.name = result.data.name;
      if (result.data.category_id) form.category_id = result.data.category_id;
      if (result.data.unit_id) form.unit_id = result.data.unit_id;
      if (result.data.purchase_price) form.purchase_price = Number(result.data.purchase_price);
      if (result.data.selling_price) form.selling_price = Number(result.data.selling_price);
      if (result.data.description) form.description = result.data.description;
    } else if (result.source === 'external' && result.data) {
      notificationStore.success('¡Producto localizado en catálogo global!');
      form.name = result.data.name;
      if (result.data.description) {
        form.description = result.data.description;
      }
    } else {
      notificationStore.info('Código no registrado previamente. Complete los datos.');
    }
  } catch {
    notificationStore.warning('No se pudo verificar el código de barras en línea.');
  } finally {
    isLookingUpBarcode.value = false;
  }
}

/**
 * Guarda o actualiza el producto
 */
async function submit() {
  if (!formRef.value) return;

  const { valid } = await formRef.value.validate();
  if (!valid) {
    notificationStore.warning('Por favor revise los campos con error.');
    return;
  }

  isSaving.value = true;
  try {
    const payload: CreateProductPayload = {
      sku: form.sku.trim(),
      name: form.name.trim(),
      category_id: form.category_id,
      unit_id: form.unit_id,
      purchase_price: Number(form.purchase_price),
      selling_price: Number(form.selling_price),
      quantity: Number(form.quantity),
      min_stock: Number(form.min_stock),
      is_active: Boolean(form.is_active),
      description: form.description ? String(form.description).trim() : null,
      notes: form.notes ? String(form.notes).trim() : null,
    };

    if (isEditing.value && productId.value) {
      await catalogService.updateProduct(productId.value, payload);
      notificationStore.success('Producto actualizado exitosamente.');
    } else {
      await catalogService.createProduct(payload);
      notificationStore.success('Producto registrado en el inventario con éxito.');
    }

    router.push({ name: 'products.index' });
  } catch (error: any) {
    notificationStore.error(error?.response?.data?.message || 'Error al persistir el producto.');
  } finally {
    isSaving.value = false;
  }
}

onMounted(() => {
  loadInitialData();
});
</script>

<style scoped>
.font-mono {
  font-family: monospace;
}
.gap-1 {
  gap: 4px;
}
.gap-2 {
  gap: 8px;
}
.gap-3 {
  gap: 12px;
}
</style>