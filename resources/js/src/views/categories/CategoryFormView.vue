<template>
  <div class="category-form-view w-100">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6">
      <div class="d-flex align-center gap-3">
        <v-btn
          icon="mdi-arrow-left"
          variant="text"
          density="comfortable"
          :to="{ name: 'categories.index' }"
          title="Regresar al listado"
        />
        <div>
          <h1 class="text-h5 font-weight-bold">
            {{ isEditing ? 'Editar Categoría' : 'Nueva Categoría' }}
          </h1>
          <p class="text-caption text-medium-emphasis">
            {{ isEditing ? 'Modifique la información de la categoría seleccionada.' : 'Complete los campos para registrar una nueva familia de productos.' }}
          </p>
        </div>
      </div>

      <div class="d-flex align-center gap-2 mt-3 mt-sm-0">
        <v-btn
          variant="tonal"
          color="secondary"
          size="small"
          :to="{ name: 'categories.index' }"
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
          {{ isEditing ? 'Actualizar' : 'Guardar' }}
        </v-btn>
      </div>
    </div>

    <!-- Skeleton durante la carga de edición -->
    <v-card v-if="isLoading" elevation="0" border rounded="lg" class="pa-6">
      <v-skeleton-loader type="article, actions" />
    </v-card>

    <!-- Formulario Principal -->
    <v-card v-else elevation="0" border rounded="lg" class="pa-6">
      <v-form ref="formRef" @submit.prevent="submit">
        <v-row>
          <v-col cols="12" md="8">
            <v-text-field
              v-model="form.name"
              label="Nombre de la Categoría *"
              placeholder="Ej. Lácteos y Embutidos"
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-tag-outline"
              :rules="[rules.required, rules.maxLength(100)]"
              class="mb-3"
            />

            <v-textarea
              v-model="form.description"
              label="Descripción (Opcional)"
              placeholder="Notas descriptivas sobre qué productos abarcará esta categoría..."
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-text-box-outline"
              rows="4"
              counter="255"
              :rules="[rules.maxLength(255)]"
              hint="Máximo 255 caracteres."
              persistent-hint
            />
          </v-col>

          <v-col cols="12" md="4">
            <v-card elevation="0" border rounded="lg" class="pa-4 bg-surface-light">
              <span class="text-caption font-weight-bold d-block mb-2">Información de Sistema</span>
              <p class="text-caption text-medium-emphasis mb-3">
                El <strong>slug</strong> se autogenera en el backend a partir del nombre para garantizar URLs amigables y consistentes.
              </p>
              <div v-if="isEditing && categorySlug" class="text-caption">
                <strong>Slug actual:</strong> <span class="font-mono">{{ categorySlug }}</span>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-form>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import catalogService from '@/api/catalog.api';
import { useNotificationStore } from '@/stores/notification';

const route = useRoute();
const router = useRouter();
const notificationStore = useNotificationStore();

const categoryId = computed(() => route.params.id as string | undefined);
const isEditing = computed(() => !!categoryId.value);

const isLoading = ref(false);
const isSaving = ref(false);
const categorySlug = ref('');
const formRef = ref<HTMLFormElement | null>(null);

const form = reactive({
  name: '',
  description: '',
});

const rules = {
  required: (v: unknown) => !!v || 'Este campo es obligatorio.',
  maxLength: (max: number) => (v: string | null | undefined) =>
    !v || v.length <= max || `Máximo ${max} caracteres permitidos.`,
};

async function loadCategory(id: string) {
  isLoading.value = true;
  try {
    const category = await catalogService.getCategoryById(id);
    form.name = category.name;
    form.description = category.description || '';
    categorySlug.value = category.slug;
  } catch (error: any) {
    notificationStore.error('Categoría no encontrada.');
    router.push({ name: 'categories.index' });
  } finally {
    isLoading.value = false;
  }
}

async function submit() {
  if (!formRef.value) return;

  const { valid } = await formRef.value.validate();
  if (!valid) return;

  isSaving.value = true;
  try {
    if (isEditing.value && categoryId.value) {
      await catalogService.updateCategory(categoryId.value, {
        name: form.name,
        description: form.description || null,
      });
      notificationStore.success('Categoría actualizada con éxito.');
    } else {
      await catalogService.createCategory({
        name: form.name,
        description: form.description || null,
      });
      notificationStore.success('Categoría registrada con éxito.');
    }

    router.push({ name: 'categories.index' });
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al guardar la categoría.'
    );
  } finally {
    isSaving.value = false;
  }
}

onMounted(() => {
  if (isEditing.value && categoryId.value) {
    loadCategory(categoryId.value);
  }
});
</script>

<style scoped>
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
</style>