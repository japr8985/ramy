<template>
  <div class="suppliers-view w-100">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Proveedores y Abastecimiento</h1>
        <p class="text-caption text-medium-emphasis">
          Directorio de proveedores comerciales y catálogo de productos suministrados.
        </p>
      </div>

      <v-btn
        color="primary"
        size="small"
        prepend-icon="mdi-domain-plus"
        class="mt-3 mt-sm-0"
        @click="openCreateModal"
      >
        Nuevo Proveedor
      </v-btn>
    </div>

    <!-- Tarjetas de Resumen / KPIs -->
    <v-row class="mb-4">
      <v-col cols="12" sm="6">
        <v-card elevation="0" border rounded="lg" class="pa-4">
          <div class="d-flex justify-space-between align-center mb-1">
            <span class="text-caption text-medium-emphasis font-weight-medium">Proveedores Registrados</span>
            <v-avatar color="primary" variant="tonal" size="28">
              <v-icon size="16">mdi-domain</v-icon>
            </v-avatar>
          </div>
          <div class="text-h5 font-weight-bold font-mono text-primary">
            {{ stats.total_suppliers }}
          </div>
          <div class="text-caption text-medium-emphasis">Empresas en la cadena de suministro</div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6">
        <v-card elevation="0" border rounded="lg" class="pa-4">
          <div class="d-flex justify-space-between align-center mb-1">
            <span class="text-caption text-medium-emphasis font-weight-medium">Productos Vinculados</span>
            <v-avatar color="secondary" variant="tonal" size="28">
              <v-icon size="16">mdi-link-variant</v-icon>
            </v-avatar>
          </div>
          <div class="text-h5 font-weight-bold font-mono text-secondary">
            {{ stats.total_products_linked }}
          </div>
          <div class="text-caption text-medium-emphasis">Ítems con canal de distribución asignado</div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Tabla Servidora de Proveedores -->
    <v-card elevation="0" border rounded="lg">
      <div class="pa-4 d-flex flex-column flex-sm-row justify-space-between align-center gap-3">
        <v-text-field
          v-model="searchQuery"
          placeholder="Buscar por empresa, contacto, correo o teléfono..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          density="compact"
          hide-details
          clearable
          class="w-100"
          style="max-width: 440px;"
          @update:model-value="onSearchInput"
          @click:clear="onSearchClear"
        />

        <v-btn
          variant="text"
          density="compact"
          icon="mdi-refresh"
          :loading="isLoading"
          title="Recargar directorio"
          @click="loadAll"
        />
      </div>

      <v-divider />

      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        v-model:sort-by="sortBy"
        :headers="headers"
        :items="suppliers"
        :items-length="totalSuppliers"
        :loading="isLoading"
        loading-text="Cargando directorio de proveedores..."
        no-data-text="No se encontraron proveedores registrados."
        density="comfortable"
        hover
        @update:options="loadSuppliers"
      >
        <!-- Empresa y Contacto -->
        <template #item.name="{ item }">
          <div class="py-2">
            <div class="font-weight-medium text-body-2">{{ item.name }}</div>
            <div class="text-caption text-medium-emphasis d-flex align-center gap-1">
              <v-icon size="14">mdi-account-outline</v-icon>
              {{ item.contact_person }}
            </div>
          </div>
        </template>

        <!-- Información de Contacto -->
        <template #item.contact="{ item }">
          <div class="py-2 text-caption">
            <div v-if="item.phone" class="font-mono text-medium-emphasis d-flex align-center gap-1">
              <v-icon size="14">mdi-phone-outline</v-icon>
              {{ item.phone }}
            </div>
            <div v-if="item.email" class="text-medium-emphasis d-flex align-center gap-1">
              <v-icon size="14">mdi-email-outline</v-icon>
              {{ item.email }}
            </div>
            <span v-if="!item.phone && !item.email" class="text-medium-emphasis font-italic">Sin datos</span>
          </div>
        </template>

        <!-- Productos Suministrados (Pivote) -->
        <template #item.products="{ item }">
          <div class="py-2 d-flex flex-wrap gap-1 align-center">
            <template v-if="item.products && item.products.length > 0">
              <v-chip
                v-for="prod in item.products.slice(0, 3)"
                :key="prod.id"
                size="x-small"
                color="primary"
                variant="tonal"
                class="font-weight-medium"
              >
                {{ prod.name }}
              </v-chip>

              <v-chip
                v-if="item.products.length > 3"
                size="x-small"
                variant="outlined"
                color="medium-emphasis"
                class="font-mono"
              >
                +{{ item.products.length - 3 }}
              </v-chip>
            </template>
            <span v-else class="text-caption text-medium-emphasis font-italic">Sin vincular</span>
          </div>
        </template>

        <!-- Dirección / Ubicación -->
        <template #item.address="{ item }">
          <span class="text-caption text-medium-emphasis text-truncate d-inline-block" style="max-width: 220px;" :title="item.address || ''">
            {{ item.address || '—' }}
          </span>
        </template>

        <!-- Acciones -->
        <template #item.actions="{ item }">
          <div class="d-flex justify-end gap-1">
            <v-btn
              icon="mdi-pencil-outline"
              size="x-small"
              variant="text"
              color="primary"
              title="Editar proveedor"
              @click="openEditModal(item)"
            />
            <v-btn
              icon="mdi-delete-outline"
              size="x-small"
              variant="text"
              color="error"
              title="Archivar proveedor"
              @click="confirmDelete(item)"
            />
          </div>
        </template>
      </v-data-table-server>
    </v-card>

    <!-- DIÁLOGO: Crear / Editar Proveedor -->
    <v-dialog v-model="formDialog" max-width="640" persistent>
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-4 d-flex align-center justify-space-between border-b">
          <div class="d-flex align-center gap-2">
            <v-icon :color="isEditing ? 'warning' : 'primary'" size="24">
              {{ isEditing ? 'mdi-domain' : 'mdi-domain-plus' }}
            </v-icon>
            <span class="text-subtitle-1 font-weight-bold">
              {{ isEditing ? 'Editar Proveedor' : 'Registrar Nuevo Proveedor' }}
            </span>
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="formDialog = false" />
        </v-card-title>

        <v-card-text class="pa-4">
          <v-form ref="formRef" @submit.prevent="submitForm">
            <v-row>
              <v-col cols="12" sm="7">
                <v-text-field
                  v-model="form.name"
                  label="Razón Social / Empresa *"
                  placeholder="Ej. Distribuidora Polar C.A."
                  variant="outlined"
                  density="compact"
                  :rules="[(v: string) => !!v?.trim() || 'El nombre es obligatorio']"
                />
              </v-col>

              <v-col cols="12" sm="5">
                <v-text-field
                  v-model="form.contact_person"
                  label="Persona de Contacto *"
                  placeholder="Ej. Pedro Pérez"
                  variant="outlined"
                  density="compact"
                  :rules="[(v: string) => !!v?.trim() || 'El contacto es obligatorio']"
                />
              </v-col>

              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.email"
                  label="Correo Electrónico"
                  type="email"
                  placeholder="ventas@proveedor.com"
                  variant="outlined"
                  density="compact"
                  :rules="[
                    (v: string) => !v || /.+@.+\..+/.test(v) || 'Indique un correo válido'
                  ]"
                />
              </v-col>

              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.phone"
                  label="Teléfono / WhatsApp"
                  placeholder="+58 414 1234567"
                  variant="outlined"
                  density="compact"
                />
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="form.address"
                  label="Dirección Física / Depósito"
                  placeholder="Zona Industrial Los Montones, Barcelona..."
                  variant="outlined"
                  density="compact"
                />
              </v-col>

              <!-- Selector Múltiple de Productos Distribuidos -->
              <v-col cols="12">
                <v-autocomplete
                  v-model="form.selected_products"
                  :items="catalogProducts"
                  item-title="name"
                  item-value="id"
                  label="Productos que Suministra"
                  placeholder="Seleccione los productos del catálogo..."
                  variant="outlined"
                  density="compact"
                  multiple
                  chips
                  closable-chips
                  clearable
                  :loading="isLoadingCatalog"
                  no-data-text="No hay productos disponibles en el catálogo."
                >
                  <template #chip="{ props, item: prodItem }">
                    <v-chip v-bind="props" size="small" color="primary" variant="tonal">
                      {{ prodItem.name }}
                      <span v-if="prodItem.sku" class="ms-1 font-mono text-caption text-medium-emphasis">
                        ({{ prodItem.sku }})
                      </span>
                    </v-chip>
                  </template>
                </v-autocomplete>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.notes"
                  label="Observaciones Comerciales"
                  placeholder="Condiciones de pago, días de entrega, despacho mínimo..."
                  variant="outlined"
                  density="compact"
                  rows="2"
                  counter="1000"
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4 bg-surface-light">
          <v-spacer />
          <v-btn variant="text" size="small" :disabled="isSubmitting" @click="formDialog = false">
            Cancelar
          </v-btn>
          <v-btn
            color="primary"
            size="small"
            :loading="isSubmitting"
            prepend-icon="mdi-content-save-outline"
            @click="submitForm"
          >
            {{ isEditing ? 'Guardar Cambios' : 'Registrar Proveedor' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DIÁLOGO: Confirmar Eliminación / Archivado -->
    <v-dialog v-model="deleteDialog" max-width="420" persistent>
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-4 d-flex align-center gap-2 border-b">
          <v-icon color="error" size="24">mdi-alert-circle-outline</v-icon>
          <span class="text-subtitle-1 font-weight-bold">Archivar Proveedor</span>
        </v-card-title>

        <v-card-text class="pa-4">
          <p class="text-body-2 mb-0">
            ¿Está seguro de que desea archivar al proveedor
            <strong>{{ supplierToDelete?.name }}</strong>? Sus órdenes históricas se conservarán intactas.
          </p>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4 bg-surface-light">
          <v-spacer />
          <v-btn variant="text" size="small" :disabled="isDeleting" @click="deleteDialog = false">
            Cancelar
          </v-btn>
          <v-btn
            color="error"
            size="small"
            :loading="isDeleting"
            prepend-icon="mdi-delete-outline"
            @click="executeDelete"
          >
            Confirmar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import purchasingService from '@/api/purchasing.api';
import catalogService from '@/api/catalog.api';
import { useNotificationStore } from '@/stores/notification';
import type {
  Supplier,
  SupplierStats,
  SaveSupplierPayload,
} from '@/types/purchasing';
import type { Product } from '@/types/catalog';

const notificationStore = useNotificationStore();

// Estados de la tabla
const isLoading = ref(false);
const suppliers = ref<Supplier[]>([]);
const totalSuppliers = ref(0);
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref<Array<{ key: string; order: 'asc' | 'desc' }>>([{ key: 'created_at', order: 'desc' }]);
const searchQuery = ref('');
let searchDebounce: ReturnType<typeof setTimeout> | null = null;

// Métricas
const stats = reactive<SupplierStats>({
  total_suppliers: 0,
  total_products_linked: 0,
});

// Catálogo de productos para el select múltiple
const catalogProducts = ref<Product[]>([]);
const isLoadingCatalog = ref(false);

const headers = [
  { title: 'Empresa / Contacto', key: 'name', sortable: true },
  { title: 'Contacto Directo', key: 'contact', sortable: false },
  { title: 'Productos Distribuidos', key: 'products', sortable: false },
  { title: 'Dirección', key: 'address', sortable: false },
  { title: '', key: 'actions', sortable: false, align: 'end' as const },
];

// Diálogo Formulario
const formDialog = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);
const formRef = ref<HTMLFormElement | null>(null);
const editingId = ref<string | null>(null);

const form = reactive<SaveSupplierPayload>({
  name: '',
  contact_person: '',
  email: '',
  phone: '',
  address: '',
  notes: '',
  selected_products: [],
});

// Diálogo Eliminación
const deleteDialog = ref(false);
const isDeleting = ref(false);
const supplierToDelete = ref<Supplier | null>(null);

async function loadAll() {
  await Promise.all([loadStats(), loadSuppliers()]);
}

async function loadStats() {
  try {
    const data = await purchasingService.getSupplierStats();
    stats.total_suppliers = data.total_suppliers;
    stats.total_products_linked = data.total_products_linked;
  } catch {
    // Silencioso
  }
}

async function loadCatalog() {
  if (catalogProducts.value.length > 0) return;
  isLoadingCatalog.value = true;
  try {
    const response = await catalogService.getProducts({ per_page: 250 });
    catalogProducts.value = response.data;
  } catch {
    notificationStore.warning('Error al precargar lista de productos del catálogo.');
  } finally {
    isLoadingCatalog.value = false;
  }
}

async function loadSuppliers() {
  isLoading.value = true;
  try {
    const sortField = sortBy.value.length ? sortBy.value[0].key : 'created_at';
    const sortDirection = sortBy.value.length ? sortBy.value[0].order : 'desc';

    const response = await purchasingService.getSuppliers({
      page: page.value,
      per_page: itemsPerPage.value,
      search: searchQuery.value || undefined,
      sort_field: sortField,
      sort_direction: sortDirection,
    });

    suppliers.value = response.data;
    totalSuppliers.value = response.total;
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al obtener proveedores.'
    );
  } finally {
    isLoading.value = false;
  }
}

function onSearchInput() {
  if (searchDebounce) clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    page.value = 1;
    loadSuppliers();
  }, 350);
}

function onSearchClear() {
  searchQuery.value = '';
  page.value = 1;
  loadSuppliers();
}

// Acciones del Formulario
async function openCreateModal() {
  resetForm();
  isEditing.value = false;
  editingId.value = null;
  formDialog.value = true;
  await loadCatalog();
}

async function openEditModal(supplier: Supplier) {
  resetForm();
  isEditing.value = true;
  editingId.value = supplier.id;

  form.name = supplier.name;
  form.contact_person = supplier.contact_person;
  form.email = supplier.email || '';
  form.phone = supplier.phone || '';
  form.address = supplier.address || '';
  form.notes = supplier.notes || '';
  form.selected_products = (supplier.products || []).map((p) => p.id);

  formDialog.value = true;
  await loadCatalog();
}

function resetForm() {
  form.name = '';
  form.contact_person = '';
  form.email = '';
  form.phone = '';
  form.address = '';
  form.notes = '';
  form.selected_products = [];
}

async function submitForm() {
  if (!formRef.value) return;

  const { valid } = await formRef.value.validate();
  if (!valid) return;

  isSubmitting.value = true;
  try {
    const payload: SaveSupplierPayload = {
      name: form.name.trim(),
      contact_person: form.contact_person.trim(),
      email: form.email?.trim() || null,
      phone: form.phone?.trim() || null,
      address: form.address?.trim() || null,
      notes: form.notes?.trim() || null,
      selected_products: form.selected_products || [],
    };

    if (isEditing.value && editingId.value) {
      await purchasingService.updateSupplier(editingId.value, payload);
      notificationStore.success('¡Proveedor actualizado con éxito!');
    } else {
      await purchasingService.createSupplier(payload);
      notificationStore.success('¡Nuevo proveedor añadido a la cadena de suministro!');
    }

    formDialog.value = false;
    await loadAll();
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al guardar los datos del proveedor.'
    );
  } finally {
    isSubmitting.value = false;
  }
}

// Eliminación
function confirmDelete(supplier: Supplier) {
  supplierToDelete.value = supplier;
  deleteDialog.value = true;
}

async function executeDelete() {
  if (!supplierToDelete.value) return;

  isDeleting.value = true;
  try {
    await purchasingService.deleteSupplier(supplierToDelete.value.id);
    notificationStore.success('Proveedor archivado con éxito.');
    deleteDialog.value = false;
    supplierToDelete.value = null;
    await loadAll();
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al archivar proveedor.',
    );
  } finally {
    isDeleting.value = false;
  }
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