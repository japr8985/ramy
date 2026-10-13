<template>
  <div class="category-scroll-container">
    <div class="category-chips-track">
      <!-- Chip 'Todos' -->
      <v-chip
        :color="selectedCategory === null ? 'primary' : undefined"
        :variant="selectedCategory === null ? 'flat' : 'outlined'"
        size="small"
        class="category-chip"
        :class="{ 'chip-active': selectedCategory === null }"
        @click="emit('select', null)"
      >
        <v-icon v-if="selectedCategory === null" start size="14">mdi-check</v-icon>
        Todos
      </v-chip>

      <!-- Chips de Categorías dinámicas -->
      <v-chip
        v-for="cat in categories"
        :key="cat.id"
        :color="selectedCategory === cat.id ? 'primary' : undefined"
        :variant="selectedCategory === cat.id ? 'flat' : 'outlined'"
        size="small"
        class="category-chip"
        :class="{ 'chip-active': selectedCategory === cat.id }"
        @click="emit('select', cat.id)"
      >
        <v-icon v-if="selectedCategory === cat.id" start size="14">mdi-check</v-icon>
        {{ cat.name }}
      </v-chip>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Category } from '@/types/catalog';

defineProps<{
  categories: Category[];
  selectedCategory: string | null;
}>();

const emit = defineEmits<{
  (e: 'select', categoryId: string | null): void;
}>();
</script>

<style scoped>
.category-scroll-container {
  width: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch; /* Inercia táctil suave en iOS/Android */
  scrollbar-width: none; /* Firefox */
  padding: 4px 0;
}

.category-scroll-container::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Edge */
}

.category-chips-track {
  display: flex;
  align-items: center;
  gap: 8px;
  width: max-content; /* Obliga a la pista a expandirse según la suma del ancho de chips */
  padding: 0 2px;
}

.category-chip {
  flex-shrink: 0 !important; /* REGLA CLAVE: Jamás se deforman ni se aplastan */
  font-weight: 500;
  user-select: none;
  cursor: pointer;
  transition: all 0.15s ease;
  height: 28px !important;
}

/* En tema oscuro, el borde outlined debe ser bien definido y el texto blanco legible */
:deep(.v-chip--variant-outlined) {
  border-color: rgba(255, 255, 255, 0.22) !important;
  color: rgba(255, 255, 255, 0.88) !important;
  background-color: rgba(255, 255, 255, 0.03) !important;
}

.chip-active {
  font-weight: 700 !important;
}
</style>