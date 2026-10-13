import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import salesService from '@/api/sale.api';
import { useNotificationStore } from '@/stores/notification';
import type { Product } from '@/types/catalog';
import type { PaymentMethod, ProcessSalePayload, Sale } from '@/types/sales';

export interface CartItem {
  product: Product;
  quantity: number;
  price: number;
}

export const usePosStore = defineStore('pos', () => {
  const notificationStore = useNotificationStore();

  // --- Estado Central del Carrito y Venta ---
  const cart = ref<CartItem[]>([]);
  const selectedCustomerId = ref<string | null>(null);
  const paymentMethod = ref<PaymentMethod>('cash');
  const totalDiscount = ref<number>(0);
  const initialPayment = ref<number>(0);
  const exchangeRate = ref<number>(1.0);
  const isProcessing = ref<boolean>(false);

  // --- Getters / Cálculos Computados ---

  // Conteo de ítems únicos
  const itemCount = computed(() => cart.value.length);

  // Total de unidades físicas en la cesta (ideal para badge móvil)
  const totalUnits = computed(() =>
    cart.value.reduce((acc, item) => acc + item.quantity, 0)
  );

  const subtotal = computed(() =>
    cart.value.reduce((acc, item) => acc + item.price * item.quantity, 0)
  );

  const total = computed(() =>
    Math.max(0, subtotal.value - totalDiscount.value)
  );

  const totalVes = computed(() =>
    total.value * exchangeRate.value
  );

  const remainingBalance = computed(() =>
    Math.max(0, total.value - initialPayment.value)
  );

  const canCheckout = computed(() => {
    if (cart.value.length === 0 || isProcessing.value) return false;
    if (paymentMethod.value === 'credit' && !selectedCustomerId.value) return false;
    return true;
  });

  // --- Validaciones Internas ---

  function validateDiscount() {
    if (totalDiscount.value < 0) totalDiscount.value = 0;
    if (totalDiscount.value > subtotal.value) totalDiscount.value = subtotal.value;
  }

  function validateInitialPayment() {
    if (initialPayment.value < 0) initialPayment.value = 0;
    if (initialPayment.value > total.value) initialPayment.value = total.value;
  }

  function setExchangeRate(rate: number) {
    exchangeRate.value = rate > 0 ? rate : 1.0;
  }

  // --- Operaciones de la Cesta ---

  function addToCart(product: Product): boolean {
    const stockAvailable = Number(product.quantity);

    if (stockAvailable <= 0) {
      notificationStore.warning(`El producto "${product.name}" no tiene existencias disponibles.`);
      return false;
    }

    const existing = cart.value.find(i => i.product.id === product.id);

    if (existing) {
      if (existing.quantity >= stockAvailable) {
        notificationStore.warning(`Máximo stock disponible alcanzado (${stockAvailable} unds).`);
        return false;
      }
      existing.quantity += 1;
    } else {
      cart.value.push({
        product,
        quantity: 1,
        price: Number(product.selling_price),
      });
    }

    validateDiscount();
    validateInitialPayment();
    return true;
  }

  function updateItemQuantity(productId: string, newQuantity: number) {
    const index = cart.value.findIndex(i => i.product.id === productId);
    if (index === -1) return;

    if (newQuantity <= 0) {
      cart.value.splice(index, 1);
    } else {
      const available = Number(cart.value[index].product.quantity);
      if (newQuantity > available) {
        notificationStore.warning(`Solo quedan ${available} unidades en inventario.`);
        return;
      }
      cart.value[index].quantity = newQuantity;
    }

    validateDiscount();
    validateInitialPayment();
  }

  function removeFromCart(productId: string) {
    cart.value = cart.value.filter(i => i.product.id !== productId);
    validateDiscount();
    validateInitialPayment();
  }

  function clearCart() {
    cart.value = [];
    totalDiscount.value = 0;
    initialPayment.value = 0;
    selectedCustomerId.value = null;
    paymentMethod.value = 'cash';
  }

  // --- Procesamiento de la Venta ---

  async function submitSale(): Promise<Sale | null> {
    if (cart.value.length === 0) {
      notificationStore.warning('Agregue al menos un producto a la orden.');
      return null;
    }

    if (paymentMethod.value === 'credit' && !selectedCustomerId.value) {
      notificationStore.error('Las ventas a crédito deben asociarse obligatoriamente a un cliente.');
      return null;
    }

    isProcessing.value = true;
    try {
      const payload: ProcessSalePayload = {
        items: cart.value.map(item => ({
          product_id: item.product.id,
          quantity: item.quantity,
          price: item.price,
        })),
        customer_id: selectedCustomerId.value || null,
        payment_method: paymentMethod.value,
        total_discount: totalDiscount.value,
        exchange_rate: exchangeRate.value,
        initial_payment: paymentMethod.value === 'credit' ? initialPayment.value : undefined,
      };

      const sale = await salesService.processSale(payload);
      notificationStore.success(`¡Venta #${sale.invoice_number} procesada con éxito!`);

      clearCart();
      return sale;
    } catch (error: any) {
      notificationStore.error(
        error?.response?.data?.message || 'Error al completar la venta.'
      );
      return null;
    } finally {
      isProcessing.value = false;
    }
  }

  return {
    // Estado
    cart,
    selectedCustomerId,
    paymentMethod,
    totalDiscount,
    initialPayment,
    exchangeRate,
    isProcessing,

    // Getters
    itemCount,
    totalUnits,
    subtotal,
    total,
    totalVes,
    remainingBalance,
    canCheckout,

    // Mutadores y acciones
    setExchangeRate,
    validateDiscount,
    validateInitialPayment,
    addToCart,
    updateItemQuantity,
    removeFromCart,
    clearCart,
    submitSale,
  };
});