import authService from '@/api/auth.api';
import { LoginPayload, User } from '@/types/auth';
import { defineStore } from 'pinia';
import { computed, ref, Ref } from 'vue';
import { useNotificationStore } from './notification';


function getStoredUser(): User | null {
  const storedUser = localStorage.getItem('auth_user');
  if (!storedUser) return null;

  try {
    return JSON.parse(storedUser) as User;
  } catch {
    localStorage.removeItem('auth_user');
    return null;
  }
}
export const useAuthStore = defineStore('auth', () => {

    const notificationStore = useNotificationStore();
    const token = ref<string | null>(localStorage.getItem('auth_token'));


    const user = ref<User | null>(getStoredUser());

    // 2. Getters computados
    const isAuthenticated = computed<boolean>(() => !!token.value);
    const currentUser = computed<User | null>(() => user.value);

    const loading: Ref<boolean> = ref(false);
    const loginForm: Ref<LoginPayload> = ref({} as LoginPayload);

    async function login(payload: LoginPayload) {
        try {
            const response = await authService.login(payload);
            
            const authToken = response.token;
            const authUser = response.user;

            token.value = authToken;
            user.value = authUser;

            localStorage.setItem('auth_token', authToken);
            localStorage.setItem('auth_user', JSON.stringify(authUser));

            notificationStore.success('Bienvenido de nuevo.');

        } catch (error) {
            clearAuth();
            throw error;
        }
    }
    async function logout(): Promise<void> {
        try {
            if (token.value) {
                await authService.logout();
            }
        } catch (error) {
            // Aunque el endpoint de backend falle o el token ya haya expirado,
            // procedemos a limpiar el estado local
            console.warn('Error revocando sesión en el servidor:', error);
        } finally {
            clearAuth();
            notificationStore.info('Has cerrado sesión correctamente.');
        }
    }

    function clearAuth(): void {
        token.value = null;
        user.value = null;
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
    }
    return {
        user,
        loading,
        loginForm,
        isAuthenticated,
        currentUser,
        login,
        logout
    }
});