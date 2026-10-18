<template>
  <v-dialog
    v-model="isOpen"
    fullscreen
    transition="dialog-bottom-transition"
    class="barcode-scanner-dialog"
  >
    <div class="scanner-container bg-black d-flex flex-column justify-space-between">
      <!-- Barra Superior Transparente -->
      <div class="scanner-header pa-4 d-flex align-center justify-space-between">
        <div class="d-flex align-center gap-2">
          <v-icon color="white" size="24">mdi-barcode-scan</v-icon>
          <span class="text-subtitle-1 font-weight-bold text-white">Escanear Código</span>
        </div>

        <div class="d-flex align-center gap-2">
          <!-- Botón Linterna / Flash (si el hardware lo soporta) -->
          <v-btn
            v-if="hasTorch"
            :icon="isTorchOn ? 'mdi-flashlight' : 'mdi-flashlight-off'"
            variant="tonal"
            color="white"
            size="small"
            @click="toggleTorch"
          />

          <!-- Cerrar -->
          <v-btn
            icon="mdi-close"
            variant="tonal"
            color="white"
            size="small"
            @click="closeScanner"
          />
        </div>
      </div>

      <!-- Viewfinder de la Cámara -->
      <div class="scanner-viewport-wrapper flex-grow-1 position-relative d-flex align-center justify-center overflow-hidden">
        <video
          ref="videoRef"
          autoplay
          playsinline
          muted
          class="camera-stream"
        />

        <!-- Marco / Guía Visual de Escaneo -->
        <div class="scan-reticle">
          <div class="laser-line" />
          <div class="corner top-left" />
          <div class="corner top-right" />
          <div class="corner bottom-left" />
          <div class="corner bottom-right" />
        </div>

        <!-- Mensaje de aviso de error si la cámara falla -->
        <div v-if="cameraError" class="camera-error-banner text-center pa-4 rounded-lg bg-surface">
          <v-icon color="error" size="36" class="mb-2">mdi-camera-off</v-icon>
          <div class="text-body-2 font-weight-bold text-error">{{ cameraError }}</div>
          <v-btn size="small" variant="outlined" color="primary" class="mt-3" @click="startCamera">
            Reintentar
          </v-btn>
        </div>
      </div>

      <!-- Barra Inferior de Estado -->
      <div class="scanner-footer pa-4 text-center">
        <span class="text-caption text-white-50">
          Apunta la cámara al código de barras o QR del producto
        </span>
      </div>
    </div>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, watch, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps<{
  modelValue: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'detected', code: string): void;
}>();

const isOpen = ref(false);
const videoRef = ref<HTMLVideoElement | null>(null);
const cameraError = ref<string | null>(null);
const hasTorch = ref(false);
const isTorchOn = ref(false);

let mediaStream: MediaStream | null = null;
let animationFrameId: number | null = null;
let barcodeDetector: any = null;
let isProcessing = false;

// Sincronizar estado con modelValue
watch(
  () => props.modelValue,
  async (val) => {
    isOpen.value = val;
    if (val) {
      await nextTick();
      startCamera();
    } else {
      stopCamera();
    }
  }
);

watch(isOpen, (val) => {
  emit('update:modelValue', val);
  if (!val) stopCamera();
});

async function initBarcodeDetector() {
  if ('BarcodeDetector' in window) {
    try {
      const formats = ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a', 'upc_e', 'qr_code'];
      barcodeDetector = new (window as any).BarcodeDetector({ formats });
    } catch {
      barcodeDetector = null;
    }
  }
}

async function startCamera() {
  cameraError.value = null;
  await initBarcodeDetector();

  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    cameraError.value = 'El navegador no tiene permisos de cámara o no está en HTTPS.';
    return;
  }

  try {
    mediaStream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: { ideal: 'environment' },
        width: { ideal: 1280 },
        height: { ideal: 720 },
      },
      audio: false,
    });

    if (videoRef.value) {
      videoRef.value.srcObject = mediaStream;
      await videoRef.value.play();

      // Chequear soporte de flash / linterna
      const track = mediaStream.getVideoTracks()[0];
      const capabilities = track.getCapabilities ? (track.getCapabilities() as any) : {};
      hasTorch.value = !!capabilities.torch;

      startDetectionLoop();
    }
  } catch (err: any) {
    cameraError.value = 'No se pudo acceder a la cámara. Verifique los permisos.';
  }
}

function startDetectionLoop() {
  if (!barcodeDetector || !videoRef.value) return;

  const detectFrame = async () => {
    if (!videoRef.value || videoRef.value.readyState < 2 || isProcessing) {
      animationFrameId = requestAnimationFrame(detectFrame);
      return;
    }

    try {
      isProcessing = true;
      const barcodes = await barcodeDetector.detect(videoRef.value);

      if (barcodes.length > 0) {
        const rawValue = barcodes[0].rawValue;
        if (rawValue) {
          emitDetected(rawValue);
          return;
        }
      }
    } catch {
      // Ignora frames vacíos o no legibles
    } finally {
      isProcessing = false;
    }

    animationFrameId = requestAnimationFrame(detectFrame);
  };

  animationFrameId = requestAnimationFrame(detectFrame);
}

function emitDetected(code: string) {
  // Feedback háptico nativo si el móvil lo soporta
  if (navigator.vibrate) {
    navigator.vibrate(100);
  }

  emit('detected', code);
  closeScanner();
}

async function toggleTorch() {
  if (!mediaStream) return;
  const track = mediaStream.getVideoTracks()[0];
  if (!track) return;

  try {
    isTorchOn.value = !isTorchOn.value;
    await (track as any).applyConstraints({
      advanced: [{ torch: isTorchOn.value }],
    });
  } catch {
    isTorchOn.value = false;
  }
}

function stopCamera() {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
    animationFrameId = null;
  }

  if (mediaStream) {
    mediaStream.getTracks().forEach((track) => track.stop());
    mediaStream = null;
  }

  if (videoRef.value) {
    videoRef.value.srcObject = null;
  }

  isTorchOn.value = false;
}

function closeScanner() {
  isOpen.value = false;
}

onBeforeUnmount(() => {
  stopCamera();
});
</script>

<style scoped>
.scanner-container {
  height: 100dvh;
  width: 100vw;
  overflow: hidden;
}

.scanner-header,
.scanner-footer {
  z-index: 20;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.7) 0%, transparent 100%);
}

.scanner-footer {
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.7) 0%, transparent 100%);
}

.camera-stream {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Mira de enfoque */
.scan-reticle {
  position: absolute;
  width: 260px;
  height: 180px;
  border-radius: 12px;
  box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.55);
  pointer-events: none;
  z-index: 10;
}

/* Esquinas de enfoque */
.corner {
  position: absolute;
  width: 20px;
  height: 20px;
  border: 3px solid #38bdf8;
}
.top-left { top: -2px; left: -2px; border-right: none; border-bottom: none; border-top-left-radius: 8px; }
.top-right { top: -2px; right: -2px; border-left: none; border-bottom: none; border-top-right-radius: 8px; }
.bottom-left { bottom: -2px; left: -2px; border-right: none; border-top: none; border-bottom-left-radius: 8px; }
.bottom-right { bottom: -2px; right: -2px; border-left: none; border-top: none; border-bottom-right-radius: 8px; }

/* Línea láser animada */
.laser-line {
  position: absolute;
  width: 100%;
  height: 2px;
  background: #38bdf8;
  box-shadow: 0 0 8px #38bdf8;
  animation: scanAnimation 2s infinite ease-in-out;
}

@keyframes scanAnimation {
  0% { top: 5%; opacity: 0.8; }
  50% { top: 95%; opacity: 1; }
  100% { top: 5%; opacity: 0.8; }
}

.camera-error-banner {
  position: absolute;
  z-index: 30;
  max-width: 80%;
}

.text-white-50 {
  color: rgba(255, 255, 255, 0.6);
}

.gap-2 { gap: 8px; }
</style>