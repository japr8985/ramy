<template>
  <v-dialog v-model="isOpen" width="500" height="500">
    <template v-slot:activator="{ props: activatorProps }">
      <v-btn
        v-bind="activatorProps"
        color="surface-variant"
        icon="mdi-account-question"
        size="small"
        variant="flat"
      />
    </template>
    
    <v-card class="d-flex align-center justify-center">
      <!-- Usamos :key para forzar al componente a reinicializarse limpiamente al cambiar de animación -->
      <DotLottieVue 
        v-if="currentAnimation"
        :key="currentIndex"
        style="height: 500px; width: 500px" 
        autoplay 
        loop 
        :data="currentAnimation" 
      />
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { DotLottieVue } from '@lottiefiles/dotlottie-vue'

import anim1 from '@/assets/animations/cat_laughin.json'
import anim2 from '@/assets/animations/cat_playing.json'
import anim3 from '@/assets/animations/dance_cat.json'
import anim4 from '@/assets/animations/winne-the-pooh.json'


const animations = [anim1, anim2, anim3, anim4]
const isOpen = ref(false)
const currentIndex = ref(0)

const currentAnimation = computed(() => {
  if (!animations.length) return null
  return animations[currentIndex.value]
})

watch(isOpen, (opened) => {
  if (opened && animations.length > 1) {
    currentIndex.value = Math.floor(Math.random() * animations.length)
  }
})
</script>