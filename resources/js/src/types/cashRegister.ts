import type { ApiResponse, PaginatedResponse, PaginationParams } from '@/types/api';

export type CashShiftStatus = 'open' | 'closed';
export type CashMovementType = 'in' | 'out';
export type CashCurrency = 'USD' | 'VES';

/**
 * Movimiento manual dentro de un turno (gasto o inyección)
 */
export interface CashMovement {
  id: string;
  cash_shift_id: string;
  user_id: string;
  type: CashMovementType;
  currency: CashCurrency;
  amount: string | number;
  concept: string;
  notes: string | null;
  created_at: string;
  updated_at: string;
  user?: {
    id: string;
    name: string;
    email: string;
  };
}

/**
 * Entidad principal de Turno de Caja (CashShift)
 */
export interface CashShift {
  id: string;
  user_id: string;
  status: CashShiftStatus;
  opened_at: string;
  closed_at: string | null;
  initial_cash_usd: string | number;
  initial_cash_ves: string | number;
  expected_cash_usd: string | number;
  expected_cash_ves: string | number;
  actual_cash_usd: string | number | null;
  actual_cash_ves: string | number | null;
  difference_usd: string | number;
  difference_ves: string | number;
  notes: string | null;
  created_at: string;
  updated_at: string;
  user?: {
    id: string;
    name: string;
    email: string;
  };
  movements?: CashMovement[];
}

/**
 * Desglose consolidado del arqueo en vivo calculado por el backend
 */
export interface CashShiftSummaryTotals {
  initial_usd: number;
  initial_ves: number;
  cash_sales_usd: number;
  cash_sales_ves: number;
  manual_in_usd: number;
  manual_in_ves: number;
  manual_out_usd: number;
  manual_out_ves: number;
  expected_usd: number;
  expected_ves: number;
}

/**
 * Respuesta del turno activo o arqueo detallado
 */
export interface CashShiftActiveData {
  shift: CashShift;
  summary: CashShiftSummaryTotals;
}

/**
 * Payloads para operaciones de caja
 */
export interface OpenCashShiftPayload {
  initial_cash_usd?: number;
  initial_cash_ves?: number;
  notes?: string | null;
}

export interface RecordCashMovementPayload {
  type: CashMovementType;
  currency: CashCurrency;
  amount: number;
  concept: string;
  notes?: string | null;
}

export interface CloseCashShiftPayload {
  actual_cash_usd: number;
  actual_cash_ves: number;
  notes?: string | null;
}

/**
 * Filtros para el historial de turnos
 */
export interface CashShiftQueryParams extends PaginationParams {
  user_id?: string;
  status?: CashShiftStatus;
}

/**
 * Envoltorios de respuesta de API
 */
export type CashShiftResponse = ApiResponse<CashShift>;
export type CashShiftActiveResponse = ApiResponse<CashShiftActiveData | null>;
export type CashMovementResponse = ApiResponse<CashMovement>;
export type CashShiftsPaginatedResponse = ApiResponse<PaginatedResponse<CashShift>>;