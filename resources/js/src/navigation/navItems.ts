export interface NavItem {
  title: string;
  icon: string;
  to: string;
  badge?: string | number;
}

export interface NavSection {
  header: string;
  items: NavItem[];
}

/**
 * 4 Accesos clave de alta rotación para la barra inferior del teléfono
 */
export const mobileBottomTabs: NavItem[] = [
  { title: 'POS', icon: 'mdi-cash-register', to: '/pos' },
  { title: 'Caja', icon: 'mdi-cash-fast', to: '/cash-register' },
  { title: 'Ventas', icon: 'mdi-receipt-text-outline', to: '/sales/history' },
  { title: 'Cobros', icon: 'mdi-account-cash-outline', to: '/receivables' },
];

/**
 * Menú completo organizado por áreas funcionales
 */
export const navigationSections: NavSection[] = [
  {
    header: 'Operación Diaria',
    items: [
      { title: 'Terminal POS', icon: 'mdi-cash-register', to: '/pos' },
      { title: 'Turno de Caja', icon: 'mdi-cash-fast', to: '/cash-register' },
      { title: 'Historial de Ventas', icon: 'mdi-receipt-text-outline', to: '/sales/history' },
      { title: 'Cuentas por Cobrar (CxC)', icon: 'mdi-account-cash-outline', to: '/receivables' },
    ],
  },
  {
    header: 'Abastecimiento',
    items: [
      { title: 'Recepción de Compras', icon: 'mdi-package-down', to: '/purchasing/purchases' },
      { title: 'Proveedores', icon: 'mdi-domain', to: '/purchasing/suppliers' },
    ],
  },
  {
    header: 'Catálogo & Directorio',
    items: [
      { title: 'Productos', icon: 'mdi-package-variant-closed', to: '/products' },
      { title: 'Categorías', icon: 'mdi-shape-outline', to: '/categories' },
      { title: 'Clientes', icon: 'mdi-account-group-outline', to: '/customers' },
    ],
  },
  {
    header: 'Control & Sistema',
    items: [
      { title: 'Historial de Turnos', icon: 'mdi-history', to: '/cash-register/history' },
      { title: 'Unidades', icon: 'mdi-weight', to: '/units'},
      { title: 'Configuración', icon: 'mdi-cog-outline', to: '/settings' },
    ],
  },
];