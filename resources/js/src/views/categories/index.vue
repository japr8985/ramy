<template>
  <div class="category-list-view w-100">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold">Categorías del Catálogo</h1>
        <p class="text-caption text-medium-emphasis">
          Gestione las agrupaciones comerciales y familias de productos del inventario.
        </p>
      </div>

      <v-btn
        color="primary"
        size="small"
        prepend-icon="mdi-plus"
        class="mt-3 mt-sm-0"
        :to="{ name: 'categories.create' }"
      >
        Nueva Categoría
      </v-btn>
    </div>

    <!-- Tabla de Datos -->
    <v-card elevation="0" border rounded="lg">
      <div class="pa-4 d-flex flex-column flex-sm-row justify-space-between align-center gap-3">
        <v-text-field
          v-model="searchQuery"
          placeholder="Buscar por nombre..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          density="compact"
          hide-details
          clearable
          style="max-width: 360px;"
          class="w-100"
          @update:model-value="onSearchInput"
          @click:clear="onSearchClear"
        />

        <v-btn
          variant="text"
          density="compact"
          icon="mdi-refresh"
          :loading="isLoading"
          title="Recargar datos"
          @click="loadCategories"
        />
      </div>

      <v-divider />

      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        v-model:sort-by="sortBy"
        :headers="headers"
        :items="categories"
        :items-length="totalCategories"
        :loading="isLoading"
        loading-text="Cargando categorías..."
        no-data-text="No se encontraron categorías registradas."
        density="comfortable"
        hover
        @update:options="loadCategories"
      >
        <template #item.name="{ item }">
          <div class="py-2">
            <span class="font-weight-medium text-body-2">{{ item.name }}</span>
            <div class="text-caption text-medium-emphasis font-mono">
              /{{ item.slug }}
            </div>
          </div>
        </template>

        <template #item.description="{ item }">
          <span class="text-body-2 text-medium-emphasis">
            {{ item.description || '—' }}
          </span>
        </template>

        <template #item.products_count="{ item }">
          <v-chip
            size="small"
            :color="item.products_count && item.products_count > 0 ? 'primary' : 'surface-variant'"
            variant="tonal"
            class="font-weight-medium"
          >
            {{ item.products_count || 0 }} productos
          </v-chip>
        </template>

        <template #item.actions="{ item }">
          <div class="d-flex justify-end gap-1">
            <v-btn
              icon="mdi-pencil-outline"
              size="x-small"
              variant="text"
              color="primary"
              title="Editar"
              :to="{ name: 'categories.edit', params: { id: item.id } }"
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

    <!-- Diálogo ligero de confirmación de eliminación -->
    <v-dialog v-model="deleteDialog" max-width="440">
      <v-card rounded="lg" elevation="3">
        <v-card-title class="pa-5 pb-3 d-flex align-center gap-2">
          <v-icon color="warning" size="24">mdi-alert-circle-outline</v-icon>
          <span class="text-subtitle-1 font-weight-bold">Confirmar Eliminación</span>
        </v-card-title>

        <v-card-text class="px-5 py-3">
          <p class="text-body-2 mb-2">
            ¿Desea eliminar la categoría <strong>{{ targetCategory?.name }}</strong>?
          </p>
          <v-alert
            v-if="targetCategory && (targetCategory.products_count ?? 0) > 0"
            type="error"
            variant="tonal"
            density="compact"
            rounded="lg"
            class="text-caption mt-2"
          >
            Tiene {{ targetCategory.products_count }} productos asociados. El servidor denegará la acción.
          </v-alert>
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
import catalogService from '@/api/catalog.api';
import { useNotificationStore } from '@/stores/notification';
import type { Category } from '@/types/catalog';

const notificationStore = useNotificationStore();

const categories = ref<Category[]>([]);
const totalCategories = ref(0);
const isLoading = ref(false);
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref<Array<{ key: string; order: 'asc' | 'desc' }>>([{ key: 'name', order: 'asc' }]);
const searchQuery = ref('');
let searchDebounceTimeout: ReturnType<typeof setTimeout> | null = null;

const headers = [
  { title: 'Nombre / Slug', key: 'name', sortable: true },
  { title: 'Descripción', key: 'description', sortable: false },
  { title: 'Productos', key: 'products_count', sortable: false, align: 'center' as const },
  { title: 'Acciones', key: 'actions', sortable: false, align: 'end' as const },
];

const deleteDialog = ref(false);
const isDeleting = ref(false);
const targetCategory = ref<Category | null>(null);

async function loadCategories() {
  isLoading.value = true;
  try {
    const sortField = sortBy.value.length ? sortBy.value[0].key : 'name';
    const sortDirection = sortBy.value.length ? sortBy.value[0].order : 'asc';

    const response = await catalogService.getCategories({
      page: page.value,
      per_page: itemsPerPage.value,
      search: searchQuery.value || undefined,
      sort_field: sortField,
      sort_direction: sortDirection,
    });

    categories.value = response.data;
    totalCategories.value = response.total;
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al cargar las categorías.'
    );
  } finally {
    isLoading.value = false;
  }
}

function onSearchInput() {
  if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
  searchDebounceTimeout = setTimeout(() => {
    page.value = 1;
    loadCategories();
  }, 350);
}

function onSearchClear() {
  searchQuery.value = '';
  page.value = 1;
  loadCategories();
}

function confirmDelete(category: Category) {
  targetCategory.value = category;
  deleteDialog.value = true;
}

async function executeDelete() {
  if (!targetCategory.value) return;

  isDeleting.value = true;
  try {
    await catalogService.deleteCategory(targetCategory.value.id);
    notificationStore.success('Categoría eliminada correctamente.');
    deleteDialog.value = false;

    if (categories.value.length === 1 && page.value > 1) {
      page.value -= 1;
    }
    await loadCategories();
  } catch (error: any) {
    notificationStore.warning(
      error?.response?.data?.message || 'No fue posible eliminar la categoría.',
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