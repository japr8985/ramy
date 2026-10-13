import http from '@/plugins/axios';
import type { PaginatedResponse } from '@/types/api';
import type {
  Supplier,
  SupplierStats,
  Purchase,
  SaveSupplierPayload,
  ProcessPurchasePayload,
  SupplierQueryParams,
  PurchaseQueryParams,
  SupplierResponse,
  SuppliersListResponse,
  SuppliersPaginatedResponse,
  SupplierStatsResponse,
  PurchaseResponse,
  PurchasesPaginatedResponse,
} from '@/types/purchasing';

class PurchasingService {
  private readonly suppliersUrl = '/api/purchasing/suppliers';
  private readonly purchasesUrl = '/api/purchasing/purchases';

  /* =========================================================================
   * Proveedores (Suppliers)
   * ========================================================================= */

  /**
   * Obtiene el listado paginado y filtrado de proveedores.
   */
  async getSuppliers(params?: SupplierQueryParams): Promise<PaginatedResponse<Supplier>> {
    const { data } = await http.get<SuppliersPaginatedResponse>(this.suppliersUrl, {
      params,
    });
    return data.data;
  }

  /**
   * Retorna todos los proveedores sin paginar (útil para selects en compras).
   */
  async getAllSuppliers(): Promise<Supplier[]> {
    const { data } = await http.get<SuppliersListResponse>(`${this.suppliersUrl}/all`);
    return data.data;
  }

  /**
   * Métricas y estadísticas de proveedores y vínculos de productos.
   */
  async getSupplierStats(): Promise<SupplierStats> {
    const { data } = await http.get<SupplierStatsResponse>(`${this.suppliersUrl}/stats`);
    return data.data;
  }

  /**
   * Consulta el detalle de un proveedor por ID.
   */
  async getSupplierById(id: string): Promise<Supplier> {
    const { data } = await http.get<SupplierResponse>(`${this.suppliersUrl}/${id}`);
    return data.data;
  }

  /**
   * Registra un nuevo proveedor en el sistema.
   */
  async createSupplier(payload: SaveSupplierPayload): Promise<Supplier> {
    const { data } = await http.post<SupplierResponse>(this.suppliersUrl, payload);
    return data.data;
  }

  /**
   * Actualiza los datos y productos vinculados de un proveedor existente.
   */
  async updateSupplier(id: string, payload: SaveSupplierPayload): Promise<Supplier> {
    const { data } = await http.put<SupplierResponse>(`${this.suppliersUrl}/${id}`, payload);
    return data.data;
  }

  /**
   * Archiva (soft-delete) un proveedor.
   */
  async deleteSupplier(id: string): Promise<void> {
    await http.delete(`${this.suppliersUrl}/${id}`);
  }

  /* =========================================================================
   * Órdenes de Compra y Recepción (Purchases)
   * ========================================================================= */

  /**
   * Obtiene el historial paginado de compras registradas.
   */
  async getPurchases(params?: PurchaseQueryParams): Promise<PaginatedResponse<Purchase>> {
    const { data } = await http.get<PurchasesPaginatedResponse>(this.purchasesUrl, {
      params,
    });
    return data.data;
  }

  /**
   * Consulta el detalle de una compra con sus renglones y productos.
   */
  async getPurchaseById(id: string): Promise<Purchase> {
    const { data } = await http.get<PurchaseResponse>(`${this.purchasesUrl}/${id}`);
    return data.data;
  }

  /**
   * Registra una compra y efectúa el ingreso inmediato al inventario.
   */
  async receivePurchase(payload: ProcessPurchasePayload): Promise<Purchase> {
    const { data } = await http.post<PurchaseResponse>(this.purchasesUrl, payload);
    return data.data;
  }
}

export const purchasingService = new PurchasingService();
export default purchasingService;