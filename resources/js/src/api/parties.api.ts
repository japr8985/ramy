import http from '@/plugins/axios';
import type { ApiResponse } from '@/types/api';
import type { PaginatedResponse } from '@/types/api';
import type {
  Customer,
  CreateCustomerPayload,
  UpdateCustomerPayload,
  CustomerQueryParams,
  CustomerResponse,
  CustomersListResponse,
  CustomersPaginatedResponse,
} from '@/types/parties';

class PartiesService {
  private readonly baseUrl = '/api/parties';

  // ---------------------------------------------------------------------------
  // Customers (Clientes)
  // ---------------------------------------------------------------------------

  /**
   * Obtiene la lista paginada de clientes con filtros de búsqueda y tipo.
   */
  async getCustomers(params?: CustomerQueryParams): Promise<PaginatedResponse<Customer>> {
    const { data } = await http.get<CustomersPaginatedResponse>(`${this.baseUrl}/customers`, {
      params,
    });
    return data.data;
  }

  /**
   * Obtiene todos los clientes sin paginación (útil para selectores rápidos en ventas/POS).
   */
  async getAllCustomers(): Promise<Customer[]> {
    const { data } = await http.get<CustomersListResponse>(`${this.baseUrl}/customers`, {
      params: { all: true },
    });
    return data.data;
  }

  /**
   * Obtiene el detalle de un cliente específico por su ID (UUID).
   */
  async getCustomerById(id: string): Promise<Customer> {
    const { data } = await http.get<CustomerResponse>(`${this.baseUrl}/customers/${id}`);
    return data.data;
  }

  /**
   * Registra un nuevo cliente en el sistema.
   */
  async createCustomer(payload: CreateCustomerPayload): Promise<Customer> {
    const { data } = await http.post<CustomerResponse>(`${this.baseUrl}/customers`, payload);
    return data.data;
  }

  /**
   * Actualiza la información de un cliente existente.
   */
  async updateCustomer(id: string, payload: UpdateCustomerPayload): Promise<Customer> {
    const { data } = await http.put<CustomerResponse>(`${this.baseUrl}/customers/${id}`, payload);
    return data.data;
  }

  /**
   * Elimina un cliente por su ID (validando que no tenga ventas históricas).
   */
  async deleteCustomer(id: string): Promise<void> {
    await http.delete<ApiResponse<null>>(`${this.baseUrl}/customers/${id}`);
  }
}

export const partiesService = new PartiesService();
export default partiesService;