import http from '@/plugins/axios';
import type { PaginatedResponse } from '@/types/api';
import type {
  AccountsReceivable,
  ReceivablePayment,
  ReceivablesStats,
  RecordPaymentPayload,
  ReceivablesQueryParams,
  ReceivableResponse,
  ReceivablesPaginatedResponse,
  ReceivablesStatsResponse,
  ReceivablePaymentResponse,
} from '@/types/receivables';

class ReceivablesService {
  private readonly baseUrl = '/api/receivables/receivables';

  async getReceivables(params?: ReceivablesQueryParams): Promise<PaginatedResponse<AccountsReceivable>> {
    const { data } = await http.get<ReceivablesPaginatedResponse>(this.baseUrl, {
      params,
    });
    return data.data;
  }

  async getStats(): Promise<ReceivablesStats> {
    const { data } = await http.get<ReceivablesStatsResponse>(`${this.baseUrl}/stats`);
    return data.data;
  }

  async getReceivableById(id: string): Promise<AccountsReceivable> {
    const { data } = await http.get<ReceivableResponse>(`${this.baseUrl}/${id}`);
    return data.data;
  }

  async recordPayment(id: string, payload: RecordPaymentPayload): Promise<ReceivablePayment> {
    const { data } = await http.post<ReceivablePaymentResponse>(
      `${this.baseUrl}/${id}/payments`,
      payload
    );
    return data.data;
  }
}

export const receivablesService = new ReceivablesService();
export default receivablesService;