import http from '@/plugins/axios';
import type { PaginatedResponse } from '@/types/api';
import type {
  Sale,
  ProcessSalePayload,
  SaleQueryParams,
  SaleResponse,
  SalesPaginatedResponse,
} from '@/types/sales';

class SalesService {
  private readonly baseUrl = '/api/sales';

  /**
   * Obtiene el listado paginado del historial de ventas con filtros de fecha, cliente o método de pago.
   */
  async getSales(params?: SaleQueryParams): Promise<PaginatedResponse<Sale>> {
    const { data } = await http.get<SalesPaginatedResponse>(`${this.baseUrl}/sales`, {
      params,
    });
    return data.data;
  }

  /**
   * Obtiene el detalle completo de una venta y sus renglones por ID.
   */
  async getSaleById(id: string): Promise<Sale> {
    const { data } = await http.get<SaleResponse>(`${this.baseUrl}/sales/${id}`);
    return data.data;
  }

  /**
   * Procesa una venta en el POS, impacta caja y descuenta inventario de forma atómica.
   */
  async processSale(payload: ProcessSalePayload): Promise<Sale> {
    const { data } = await http.post<SaleResponse>(`${this.baseUrl}/sales`, payload);
    return data.data;
  }
}

export const salesService = new SalesService();
export default salesService;