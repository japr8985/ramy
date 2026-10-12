import type { ApiResponse } from '@/types/api';
import type { PaginatedResponse, PaginationParams } from '@/types/api';

export type CustomerType = 'Individual' | 'Corporate';

/**
 * Entidad de Cliente (Customer)
 */
export interface Customer {
  id: string;
  rif_ci: string;
  name: string;
  type: CustomerType;
  email: string | null;
  phone: string | null;
  address: string | null;
  notes: string | null;
  initials?: string;
  sales_count?: number;
  created_at?: string;
  updated_at?: string;
  deleted_at?: string | null;
}

/**
 * Payload para creación de cliente
 */
export interface CreateCustomerPayload {
  rif_ci: string;
  name: string;
  type: CustomerType;
  email?: string | null;
  phone?: string | null;
  address?: string | null;
  notes?: string | null;
}

/**
 * Payload para actualización de cliente
 */
export interface UpdateCustomerPayload extends CreateCustomerPayload {}

/**
 * Parámetros de consulta y filtrado para el listado paginado
 */
export interface CustomerQueryParams extends PaginationParams {
  type?: CustomerType;
}

/**
 * Envoltorios de respuesta HTTP tipados
 */
export type CustomerResponse = ApiResponse<Customer>;
export type CustomersListResponse = ApiResponse<Customer[]>;
export type CustomersPaginatedResponse = ApiResponse<PaginatedResponse<Customer>>;