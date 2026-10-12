import http from "@/plugins/axios";
import { ApiResponse } from "@/types/api";
import { LoginPayload, LoginResponse } from "@/types/auth";

class AuthService {
    async login(payload: LoginPayload): Promise<LoginResponse>
    {
        const { data: response } = await http.post<ApiResponse<LoginResponse>>('/api/identity/login', payload);
        return response.data;
    }
    async logout(): Promise
    {
        const { data: response } = await http.post('/api/identity/logout')
        return response.data
    }
}

export const authService = new AuthService();
export default authService;