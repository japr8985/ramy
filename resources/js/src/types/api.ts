export interface ApiResponse<T = any> {
  success: boolean;
  status: number;
  message: string;
  data: T;
  errors: Record<string, string[]> | null;
}
export interface PaginatedResponse<T> {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number | null;
  last_page: number;
  last_page_url: string;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number | null;
  total: number;
}

export interface PaginationParams {
  page?: number;
  per_page?: number;
  search?: string;
  sort_field?: string;
  sort_direction?: 'asc' | 'desc';
  all?: boolean;
}