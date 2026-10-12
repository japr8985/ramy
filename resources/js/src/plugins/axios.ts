import axios, { type AxiosInstance, type InternalAxiosRequestConfig, type AxiosResponse, type AxiosError } from 'axios';
import { useNotificationStore } from '@/stores/notification';
import router from '@/router';

interface ApiResponseContract<T = any> {
  success: boolean;
  status: number;
  message: string;
  data: T;
  errors: Record<string, string[]> | null;
}

const instance: AxiosInstance = axios.create({
  baseURL: '/',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  timeout: 10000,
});

// Interceptor Request: Inyecta el token Bearer
instance.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    
    const token = localStorage.getItem('auth_token');
    
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Interceptor Response: Manejo global de errores y notificación automática
instance.interceptors.response.use(
  (response: AxiosResponse<ApiResponseContract>) => {
    return response;
  },
  (error: AxiosError<ApiResponseContract>) => {
    const notificationStore = useNotificationStore();
    const responseData = error.response?.data;
    const status = error.response?.status;
    
    if (!error.response) {
      // Error de red / sin conexión
      notificationStore.error('No hay conexión con el servidor. Verifica tu red.');
      return Promise.reject(error);
    }

    switch (status) {
      case 401:
        localStorage.removeItem('auth_token');
        notificationStore.error(responseData?.message || 'Sesión expirada o no autorizada.');
        if (router.currentRoute.value.name !== 'login') {
          router.push({ name: 'login' });
        }
        break;

      case 422:
        // Los errores de validación suelen mostrarse en los inputs del formulario,
        // pero notificamos el mensaje global si está disponible
        notificationStore.warning(responseData?.message || 'Revisa los campos del formulario.');
        break;

      case 403:
        notificationStore.error(responseData?.message || 'Acceso denegado.');
        break;

      case 404:
        notificationStore.error(responseData?.message || 'El recurso solicitado no fue encontrado.');
        break;

      case 500:
      default:
        notificationStore.error(responseData?.message || 'Ocurrió un error inesperado en el servidor.');
        break;
    }

    return Promise.reject(error);
  }
);

export default instance;