import http from '@/plugins/axios';
import type { SettingsMap, SettingsResponse } from '@/types/settings';

class SettingsService {
  private readonly endpoint = '/api/settings';

  /**
   * Obtiene el mapa clave-valor de configuraciones del sistema.
   */
  async getSettings(): Promise<SettingsMap> {
    const { data } = await http.get<SettingsResponse>(this.endpoint);
    return data.data;
  }

  /**
   * Actualiza las configuraciones enviando el mapa dinámico.
   * Retorna el mapa actualizado devuelto por el backend.
   */
  async updateSettings(payload: SettingsMap): Promise<SettingsMap> {
    const { data } = await http.put<SettingsResponse>(this.endpoint, payload);
    return data.data;
  }
}

export const settingsService = new SettingsService();
export default settingsService;