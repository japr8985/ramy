import http from '@/plugins/axios';
import type { PaginationParams, PaginatedResponse } from '@/types/api';
import type { ApiResponse } from '@/types/api';
import type {
  Unit,
  CreateUnitPayload,
  Category,
  CreateCategoryPayload,
  UpdateCategoryPayload,
  UnitsResponse,
  UnitResponse,
  CategoriesListResponse,
  CategoriesPaginatedResponse,
  CategoryResponse,
  Product,
  CreateProductPayload,
  UpdateProductPayload,
  ProductQueryParams,
  ProductKpis,
  BarcodeLookupResult,
  ProductsPaginatedResponse,
  ProductResponse,
  ProductKpisResponse,
  BarcodeLookupResponse,
} from '@/types/catalog';

class CatalogService {
  private readonly baseUrl = '/api/catalog';

  // ---------------------------------------------------------------------------
  // Units (Unidades de Medida)
  // ---------------------------------------------------------------------------

  async getUnits(): Promise<Unit[]> {
    const { data } = await http.get<UnitsResponse>(`${this.baseUrl}/units`);
    return data.data;
  }

  async createUnit(payload: CreateUnitPayload): Promise<Unit> {
    const { data } = await http.post<UnitResponse>(`${this.baseUrl}/units`, payload);
    return data.data;
  }

  // ---------------------------------------------------------------------------
  // Categories (Categorías)
  // ---------------------------------------------------------------------------

  async getCategories(params?: PaginationParams): Promise<PaginatedResponse<Category>> {
    const { data } = await http.get<CategoriesPaginatedResponse>(`${this.baseUrl}/categories`, {
      params,
    });
    return data.data;
  }
  /**
 * Obtiene los detalles de una categoría por su ID (UUID).
 */
  async getCategoryById(id: string): Promise<Category> {
    const { data } = await http.get<CategoryResponse>(`${this.baseUrl}/categories/${id}`);
    return data.data;
  }

  async getAllCategories(): Promise<Category[]> {
    const { data } = await http.get<CategoriesListResponse>(`${this.baseUrl}/categories`, {
      params: { all: true },
    });
    return data.data;
  }

  async createCategory(payload: CreateCategoryPayload): Promise<Category> {
    const { data } = await http.post<CategoryResponse>(`${this.baseUrl}/categories`, payload);
    return data.data;
  }

  async updateCategory(id: string, payload: UpdateCategoryPayload): Promise<Category> {
    const { data } = await http.put<CategoryResponse>(`${this.baseUrl}/categories/${id}`, payload);
    return data.data;
  }

  async deleteCategory(id: string): Promise<void> {
    await http.delete<ApiResponse<null>>(`${this.baseUrl}/categories/${id}`);
  }

  // ---------------------------------------------------------------------------
  // Products (Productos)
  // ---------------------------------------------------------------------------

  /**
   * Obtiene la lista paginada de productos con filtros por categoría, búsqueda y ordenamiento.
   */
  async getProducts(params?: ProductQueryParams): Promise<PaginatedResponse<Product>> {
    const { data } = await http.get<ProductsPaginatedResponse>(`${this.baseUrl}/products`, {
      params,
    });
    return data.data;
  }

  /**
   * Obtiene los indicadores agregados del inventario (total, stock crítico, valor acumulado).
   */
  async getProductKpis(): Promise<ProductKpis> {
    const { data } = await http.get<ProductKpisResponse>(`${this.baseUrl}/products/kpis`);
    return data.data;
  }

  /**
   * Obtiene un producto por su UUID incluyendo sus relaciones 'category' y 'unit'.
   */
  async getProductById(id: string): Promise<Product> {
    const { data } = await http.get<ProductResponse>(`${this.baseUrl}/products/${id}`);
    return data.data;
  }

  /**
   * Registra un nuevo producto en el catálogo.
   */
  async createProduct(payload: CreateProductPayload): Promise<Product> {
    const { data } = await http.post<ProductResponse>(`${this.baseUrl}/products`, payload);
    return data.data;
  }

  /**
   * Actualiza los datos de un producto existente.
   */
  async updateProduct(id: string, payload: UpdateProductPayload): Promise<Product> {
    const { data } = await http.put<ProductResponse>(`${this.baseUrl}/products/${id}`, payload);
    return data.data;
  }

  /**
   * Remueve un producto del inventario (validando que no tenga ventas históricas).
   */
  async deleteProduct(id: string): Promise<void> {
    await http.delete<ApiResponse<null>>(`${this.baseUrl}/products/${id}`);
  }

  /**
   * Resuelve información de un código de barras en base de datos local o en OpenFoodFacts.
   */
  async lookupBarcode(barcode: string): Promise<BarcodeLookupResult> {
    const { data } = await http.get<BarcodeLookupResponse>(
      `${this.baseUrl}/products/lookup-barcode/${encodeURIComponent(barcode)}`
    );
    return data.data;
  }
}

export const catalogService = new CatalogService();
export default catalogService;