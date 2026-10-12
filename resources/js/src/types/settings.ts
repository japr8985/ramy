import type { ApiResponse } from '@/types/api';

/**
 * Almacén clave-valor dinámico de configuraciones.
 * Permite cualquier clave string con valor string o null: { [key: string]: string | null }
 */
export type SettingsMap = Record<string, string | null>;

/**
 * Claves conocidas del sistema para proveer autocompletado e IntelliSense,
 * sin restringir que el mapa reciba claves adicionales dinámicas.
 */
export type KnownSettingKey = 
  | 'business_name'
  | 'tax_id'
  | 'currency'
  | 'exchange_rate'
  | 'invoice_footer';

/**
 * Estado fuertemente tipado pero extensible:
 * Garantiza las claves conocidas mientras permite cualquier clave extra futura.
 */
export type AppSettings = SettingsMap & {
  business_name?: string;
  tax_id?: string;
  currency?: string;
  exchange_rate?: string | number;
  invoice_footer?: string | null;
};

/**
 * Payload enviado a la API: un mapa dinámico clave-valor
 */
export type UpdateSettingsPayload = SettingsMap;

/**
 * Respuesta genérica envuelta en ApiResponse
 */
export type SettingsResponse = ApiResponse<SettingsMap>;