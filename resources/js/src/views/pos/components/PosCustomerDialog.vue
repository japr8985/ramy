<template>
  <v-dialog v-model="isOpen" max-width="450" scrollable>
    <v-card rounded="xl" class="overflow-hidden">
      <!-- Cabecera -->
      <div class="pa-4 border-b d-flex align-center justify-space-between">
        <div class="d-flex align-center gap-2">
          <v-icon color="primary">mdi-account-search-outline</v-icon>
          <span class="text-subtitle-1 font-weight-bold">Seleccionar Cliente</span>
        </div>
        <v-btn icon="mdi-close" variant="text" size="small" @click="isOpen = false" />
      </div>

      <!-- Buscador -->
      <div class="pa-3 border-b bg-surface-light">
        <v-text-field
          v-model="searchQuery"
          placeholder="Buscar por nombre, RIF o C.I..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          density="compact"
          hide-details
          clearable
          autofocus
        />
      </div>

      <!-- Lista de Clientes con Scroll -->
      <v-card-text class="pa-2 overflow-y-auto" style="max-height: 380px;">
        <!-- Opción rápida: Consumidor Final -->
        <v-list-item
          rounded="lg"
          class="mb-1"
          :active="!posStore.selectedCustomerId"
          color="primary"
          @click="selectCustomer(null)"
        >
          <template #prepend>
            <v-avatar color="surface-variant" size="36">
              <v-icon size="20">mdi-account-outline</v-icon>
            </v-avatar>
          </template>
          <v-list-item-title class="font-weight-bold text-body-2">
            Consumidor Final
          </v-list-item-title>
          <v-list-item-subtitle class="text-caption">
            Venta sin datos fiscales registrados
          </v-list-item-subtitle>
        </v-list-item>

        <v-divider class="my-2" />

        <!-- Clientes filtrados -->
        <v-list-item
          v-for="customer in filteredCustomers"
          :key="customer.id"
          rounded="lg"
          class="mb-1"
          :active="posStore.selectedCustomerId === customer.id"
          color="primary"
          @click="selectCustomer(customer.id)"
        >
          <template #prepend>
            <v-avatar color="primary" variant="tonal" size="36">
              <span class="text-caption font-weight-bold">
                {{ customer.name.charAt(0).toUpperCase() }}
              </span>
            </v-avatar>
          </template>
          <v-list-item-title class="font-weight-bold text-body-2">
            {{ customer.name }}
          </v-list-item-title>
          <v-list-item-subtitle class="text-caption font-mono">
            {{ customer.rif_ci }}
          </v-list-item-subtitle>
        </v-list-item>

        <div v-if="filteredCustomers.length === 0" class="text-center pa-6 text-caption text-medium-emphasis">
          No se encontraron clientes que coincidan con la búsqueda.
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePosStore } from '@/stores/pos';
import type { Customer } from '@/types/parties';

const props = defineProps<{
  modelValue: boolean;
  customers: Customer[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
}>();

const posStore = usePosStore();
const searchQuery = ref('');

const isOpen = computed({
  get: () => props.modelValue,
  set: (val: boolean) => emit('update:modelValue', val),
});

const filteredCustomers = computed(() => {
  if (!searchQuery.value.trim()) return props.customers;
  const q = searchQuery.value.toLowerCase();
  return props.customers.filter(
    c => c.name.toLowerCase().includes(q) || c.rif_ci?.toLowerCase().includes(q)
  );
});

function selectCustomer(id: string | null) {
  posStore.selectedCustomerId = id;
  isOpen.value = false;
  searchQuery.value = '';
}
</script>

<style scoped>
.font-mono { font-family: monospace; }
.gap-2 { gap: 8px; }
</style>