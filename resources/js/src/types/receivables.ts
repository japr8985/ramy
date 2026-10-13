import type { ApiResponse, PaginatedResponse, PaginationParams } from '@/types/api';
import type { Customer } from '@/types/parties';
import type { Sale } from '@/types/sales';
import type { Product } from '@/types/catalog';

export type ReceivableStatus = 'pending' | 'partial' | 'paid' | 'overdue';

export interface ReceivablePayment {
  id: string;
  accounts_receivable_id: string;
  payment_date: string;
  amount_usd: string | number;
  exchange_rate: string | number;
  payment_method: string;
  notes: string | null;
  created_at: string;
  updated_at: string;
}

export interface AccountsReceivableItem {
  id: string;
  accounts_receivable_id: string;
  product_id: string;
  quantity: number;
  cost_price: string | number;
  unit_price: string | number;
  discount: string | number;
  final_price: string | number;
  subtotal: string | number;
  product?: Pick<Product, 'id' | 'name' | 'sku'>;
}

export interface AccountsReceivable {
  id: string;
  sale_id: string;
  customer_id: string;
  sale_date: string;
  due_date: string;
  total: string | number;
  paid_amount: string | number;
  balance: string | number;
  status?: ReceivableStatus;
  created_at: string;
  updated_at: string;
  customer?: Pick<Customer, 'id' | 'name' | 'rif_ci' | 'phone'>;
  sale?: Pick<Sale, 'id' | 'invoice_number' | 'total' | 'sale_date'>;
  items?: AccountsReceivableItem[];
  payments?: ReceivablePayment[];
}

export interface ReceivablesStats {
  total_receivables: number;
  overdue_amount: number;
  collected_mtd: number;
}

export interface RecordPaymentPayload {
  amount_usd: number;
  exchange_rate: number;
  payment_date: string;
  payment_method: string;
  notes?: string | null;
}

export interface ReceivablesQueryParams extends PaginationParams {
  customer_id?: string;
  status?: ReceivableStatus;
  only_with_balance?: boolean;
}

export type ReceivableResponse = ApiResponse<AccountsReceivable>;
export type ReceivablesPaginatedResponse = ApiResponse<PaginatedResponse<AccountsReceivable>>;
export type ReceivablesStatsResponse = ApiResponse<ReceivablesStats>;
export type ReceivablePaymentResponse = ApiResponse<ReceivablePayment>;