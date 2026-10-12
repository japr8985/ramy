import { defineStore } from 'pinia';
import { ref } from 'vue';

export type NotificationType = 'success' | 'error' | 'warning' | 'info';

export interface NotificationPayload {
  message: string;
  type?: NotificationType;
  timeout?: number;
}

export const useNotificationStore = defineStore('notification', () => {
  const isVisible = ref(false);
  const message = ref('');
  const type = ref<NotificationType>('info');
  const timeout = ref(4000);

  function notify(payload: NotificationPayload) {
    message.value = payload.message;
    type.value = payload.type ?? 'info';
    timeout.value = payload.timeout ?? 4000;
    isVisible.value = true;
  }

  function success(msg: string, customTimeout?: number) {
    notify({ message: msg, type: 'success', timeout: customTimeout });
  }

  function error(msg: string, customTimeout?: number) {
    notify({ message: msg, type: 'error', timeout: customTimeout });
  }

  function warning(msg: string, customTimeout?: number) {
    notify({ message: msg, type: 'warning', timeout: customTimeout });
  }

  function info(msg: string, customTimeout?: number) {
    notify({ message: msg, type: 'info', timeout: customTimeout });
  }

  function close() {
    isVisible.value = false;
  }

  return {
    isVisible,
    message,
    type,
    timeout,
    notify,
    success,
    error,
    warning,
    info,
    close,
  };
});