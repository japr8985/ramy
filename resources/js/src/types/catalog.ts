import type { ApiResponse, PaginationParams } from '@/types/api';
import type { PaginatedResponse } from '@/types/api';

/**
 * Entidad de Unidad de Medida
 */
export interface Unit {
  id: string;
  name: string;
  symbol: string;
  created_at?: string;
  updated_at?: string;
}

export interface CreateUnitPayload {
  name: string;
  symbol: string;
}

/**
 * Entidad de Categoría
 */
export interface Category {
  id: string;
  name: string;
  slug: string;
  description: string | null;
  products_count?: number;
  created_at?: string;
  updated_at?: string;
  deleted_at?: string | null;
}

export interface CreateCategoryPayload {
  name: string;
  description?: string | null;
}

export interface UpdateCategoryPayload {
  name: string;
  description?: string | null;
}

/**
 * Envoltorios de Respuesta de API
 */
export type UnitsResponse = ApiResponse<Unit[]>;
export type UnitResponse = ApiResponse<Unit>;

export type CategoriesListResponse = ApiResponse<Category[]>;
export type CategoriesPaginatedResponse = ApiResponse<PaginatedResponse<Category>>;
export type CategoryResponse = ApiResponse<Category>;

// =============================================================================
// Products
// =============================================================================

export interface Product {
  id: string;
  category_id: string;
  unit_id: string;
  sku: string;
  name: string;
  purchase_price: string | number;
  selling_price: string | number;
  quantity: number;
  min_stock: number;
  is_active: boolean;
  description: string | null;
  notes: string | null;
  created_at?: string;
  updated_at?: string;
  category?: Pick<Category, 'id' | 'name' | 'slug'>;
  unit?: Pick<Unit, 'id' | 'name' | 'symbol'>;
  sale_items_count?: number;
}

export interface CreateProductPayload {
  sku: string;
  name: string;
  category_id: string;
  unit_id: string;
  purchase_price: number | string;
  selling_price: number | string;
  quantity: number | string;
  min_stock: number | string;
  is_active?: boolean;
  description?: string | null;
  notes?: string | null;
}

export interface UpdateProductPayload extends CreateProductPayload {}

export interface ProductQueryParams extends PaginationParams {
  category_id?: string;
}

export interface ProductKpis {
  total_products: number;
  critical_stock: number;
  stock_value: number;
}

export interface BarcodeLookupData {
  id?: string;
  sku: string;
  name: string;
  category_id?: string;
  unit_id?: string;
  selling_price?: string | number;
  quantity?: number;
  description?: string | null;
}

export interface BarcodeLookupResult {
  source: 'local' | 'external' | 'none';
  exists_locally: boolean;
  data: BarcodeLookupData | null;
}

export type ProductsPaginatedResponse = ApiResponse<PaginatedResponse<Product>>;
export type ProductResponse = ApiResponse<Product>;
export type ProductKpisResponse = ApiResponse<ProductKpis>;
export type BarcodeLookupResponse = ApiResponse<BarcodeLookupResult>;