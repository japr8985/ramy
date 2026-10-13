<!-- resources/js/src/views/pos/components/PosCartDrawer.vue -->
<template>
  <v-bottom-sheet
    v-model="isOpen"
    scrollable
    inset
    class="pos-cart-bottom-sheet"
  >
    <v-card class="rounded-t-xl overflow-hidden d-flex flex-column drawer-card-viewport">
      <!-- Tirador visual de arrastre -->
      <div class="d-flex justify-center pt-2 pb-1 bg-surface-light border-b flex-shrink-0">
        <div class="sheet-drag-handle" />
      </div>

      <!-- Contenido operativo -->
      <div class="flex-grow-1 overflow-hidden d-flex flex-column" style="min-height: 0;">
        <PosCartContent
          is-mobile-sheet
          :customers="customers"
          @close="isOpen = false"
          @sale-completed="onSaleCompleted"
        />
      </div>
    </v-card>
  </v-bottom-sheet>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { Customer } from '@/types/parties';
import PosCartContent from './PosCartContent.vue';

const props = defineProps<{
  modelValue: boolean;
  customers: Customer[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'sale-completed'): void;
}>();

const isOpen = computed({
  get: () => props.modelValue,
  set: (val: boolean) => emit('update:modelValue', val),
});

function onSaleCompleted() {
  isOpen.value = false;
  emit('sale-completed');
}
</script>

<style scoped>
.drawer-card-viewport {
  height: 88dvh !important;
  max-height: 88dvh !important;
}

.sheet-drag-handle {
  width: 40px;
  height: 4px;
  background-color: rgba(255, 255, 255, 0.25);
  border-radius: 2px;
}
</style>