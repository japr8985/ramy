import http from '@/plugins/axios';
import type { PaginatedResponse } from '@/types/api';
import type {
  CashShift,
  CashMovement,
  CashShiftActiveData,
  OpenCashShiftPayload,
  RecordCashMovementPayload,
  CloseCashShiftPayload,
  CashShiftQueryParams,
  CashShiftResponse,
  CashShiftActiveResponse,
  CashMovementResponse,
  CashShiftsPaginatedResponse,
} from '@/types/cashRegister';

class CashRegisterService {
  private readonly baseUrl = '/api/cash-register/shifts';

  /**
   * Obtiene el listado histórico paginado de turnos de caja.
   */
  async getShifts(params?: CashShiftQueryParams): Promise<PaginatedResponse<CashShift>> {
    const { data } = await http.get<CashShiftsPaginatedResponse>(this.baseUrl, {
      params,
    });
    return data.data;
  }

  /**
   * Consulta el turno actualmente abierto para el cajero autenticado junto al arqueo en vivo.
   * Retorna null si no tiene una sesión de caja abierta.
   */
  async getCurrentShift(): Promise<CashShiftActiveData | null> {
    const { data } = await http.get<CashShiftActiveResponse>(`${this.baseUrl}/current`);
    return data.data;
  }

  /**
   * Abre un nuevo turno de caja con los fondos base en USD y VES.
   */
  async openShift(payload: OpenCashShiftPayload): Promise<CashShift> {
    const { data } = await http.post<CashShiftResponse>(`${this.baseUrl}/open`, payload);
    return data.data;
  }

  /**
   * Consulta el arqueo consolidado y movimientos de un turno específico por ID.
   */
  async getShiftSummary(shiftId: string): Promise<CashShiftActiveData> {
    const { data } = await http.get<CashShiftActiveResponse>(`${this.baseUrl}/${shiftId}/summary`);
    return data.data as CashShiftActiveData;
  }

  /**
   * Registra una entrada o egreso de dinero manual (gastos, sencillo, retiros).
   */
  async recordMovement(shiftId: string, payload: RecordCashMovementPayload): Promise<CashMovement> {
    const { data } = await http.post<CashMovementResponse>(
      `${this.baseUrl}/${shiftId}/movements`,
      payload
    );
    return data.data;
  }

  /**
   * Realiza el arqueo y cierre definitivo del turno congelando montos y calculando diferencias.
   */
  async closeShift(shiftId: string, payload: CloseCashShiftPayload): Promise<CashShift> {
    const { data } = await http.post<CashShiftResponse>(
      `${this.baseUrl}/${shiftId}/close`,
      payload
    );
    return data.data;
  }
}

export const cashRegisterService = new CashRegisterService();
export default cashRegisterService;