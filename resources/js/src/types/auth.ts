export interface LoginPayload {
  email: string;
  password: string;
  remember?: boolean;
}

export interface User {
  id: string | number;
  name: string;
  email: string;
}

export interface LoginResponse {
  token: string;
  user: User;
}
