import type { ApiResponse } from '@/types/api';
import type { PaginatedResponse, PaginationParams } from '@/types/api';
import type { Customer } from '@/types/parties';
import type { Product } from '@/types/catalog';

export type PaymentMethod = 'cash' | 'card' | 'credit';
export type SaleStatus = 'completed' | 'cancelled' | 'pending';

/**
 * Renglón individual de venta persistido
 */
export interface SaleItem {
  id: string;
  sale_id: string;
  product_id: string;
  quantity: number;
  price: string | number;
  subtotal: string | number;
  created_at?: string;
  updated_at?: string;
  product?: Pick<Product, 'id' | 'name' | 'sku'>;
}

/**
 * Entidad de Venta (Sale)
 */
export interface Sale {
  id: string;
  invoice_number: string;
  customer_id: string | null;
  created_by: string;
  sale_date: string;
  status: SaleStatus;
  subtotal: string | number;
  total_discount: string | number;
  total: string | number;
  exchange_rate: string | number;
  cash_received: string | number;
  change: string | number;
  payment_method: PaymentMethod;
  notes: string | null;
  created_at?: string;
  updated_at?: string;
  customer?: Pick<Customer, 'id' | 'name' | 'rif_ci'> | null;
  items?: SaleItem[];
}

/**
 * Renglón de venta para el payload de procesamiento
 */
export interface ProcessSaleItemPayload {
  product_id: string;
  quantity: number;
  price: number;
}

/**
 * Payload completo enviado para procesar una venta desde el POS
 */
export interface ProcessSalePayload {
  items: ProcessSaleItemPayload[];
  customer_id?: string | null;
  payment_method: PaymentMethod;
  total_discount?: number;
  exchange_rate: number;
  initial_payment?: number;
  notes?: string | null;
}

/**
 * Parámetros de consulta y filtrado para el historial de ventas
 */
export interface SaleQueryParams extends PaginationParams {
  status?: SaleStatus;
  payment_method?: PaymentMethod;
}

/**
 * Envoltorios de respuesta de API tipados
 */
export type SaleResponse = ApiResponse<Sale>;
export type SalesPaginatedResponse = ApiResponse<PaginatedResponse<Sale>>;