import type { ApiResponse, PaginatedResponse, PaginationParams } from '@/types/api';
import type { Product } from '@/types/catalog';

export type PurchaseStatus = 'draft' | 'received' | 'cancelled';

/**
 * Entidad de Proveedor (Supplier)
 */
export interface Supplier {
  id: string;
  name: string;
  contact_person: string;
  email: string | null;
  phone: string | null;
  address: string | null;
  notes: string | null;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  products?: Array<Pick<Product, 'id' | 'name' | 'sku'>>;
}

export interface SupplierStats {
  total_suppliers: number;
  total_products_linked: number;
}

/**
 * Renglón individual dentro de una compra
 */
export interface PurchaseItem {
  id: string;
  purchase_id: string;
  product_id: string;
  quantity: number;
  unit_price: string | number;
  selling_price: string | number | null;
  subtotal: string | number;
  created_at: string;
  updated_at: string;
  product?: Pick<Product, 'id' | 'name' | 'sku' | 'cost_price' | 'selling_price'>;
}

/**
 * Entidad de Orden / Factura de Compra (Purchase)
 */
export interface Purchase {
  id: string;
  invoice_number: string | null;
  supplier_id: string;
  purchase_date: string;
  due_date: string | null;
  total: string | number;
  status: PurchaseStatus;
  notes: string | null;
  proof_image: string | null;
  created_by: string;
  created_at: string;
  updated_at: string;
  supplier?: Pick<Supplier, 'id' | 'name' | 'contact_person' | 'email' | 'phone'>;
  creator?: {
    id: string;
    name: string;
    email?: string;
  };
  items?: PurchaseItem[];
}

/**
 * Payloads para Proveedores
 */
export interface SaveSupplierPayload {
  name: string;
  contact_person: string;
  email?: string | null;
  phone?: string | null;
  address?: string | null;
  notes?: string | null;
  selected_products?: string[];
}

/**
 * Payloads para Registro y Recepción de Compra
 */
export interface ProcessPurchaseItemPayload {
  product_id: string;
  quantity: number;
  unit_price: number;
  selling_price?: number | null;
}

export interface ProcessPurchasePayload {
  supplier_id: string;
  purchase_date: string;
  due_date?: string | null;
  invoice_number?: string | null;
  notes?: string | null;
  proof_image?: string | null;
  items: ProcessPurchaseItemPayload[];
}

/**
 * Filtros de Búsqueda y Paginación
 */
export interface SupplierQueryParams extends PaginationParams {
  search?: string;
}

export interface PurchaseQueryParams extends PaginationParams {
  search?: string;
  supplier_id?: string;
  status?: PurchaseStatus;
}

/**
 * Respuestas de API
 */
export type SupplierResponse = ApiResponse<Supplier>;
export type SuppliersListResponse = ApiResponse<Supplier[]>;
export type SuppliersPaginatedResponse = ApiResponse<PaginatedResponse<Supplier>>;
export type SupplierStatsResponse = ApiResponse<SupplierStats>;

export type PurchaseResponse = ApiResponse<Purchase>;
export type PurchasesPaginatedResponse = ApiResponse<PaginatedResponse<Purchase>>;