export interface ApiResponse<T = any> {
  success: boolean;
  status: number;
  message: string;
  data: T;
  errors: Record<string, string[]> | null;
}
