import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css'; // Si usas los iconos MDI
import { createVuetify, ThemeDefinition } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

const mereyDark: ThemeDefinition = {
  dark: true,
  colors: {
    background: '#0A0C13',
    surface: '#12141F',
    'surface-bright': '#1A1D2D',
    'surface-light': '#24283E',
    'surface-variant': '#1E2235',
    'on-surface-variant': '#94A3B8',
    primary: '#0EA5E9',           // Sky Blue
    'primary-darken-1': '#0284C7',
    secondary: '#6366F1',         // Indigo
    'secondary-darken-1': '#4F46E5',
    error: '#EF4444',
    info: '#38BDF8',
    success: '#10B981',
    warning: '#F59E0B',
  },
  variables: {
    'border-color': '#FFFFFF',
    'border-opacity': 0.08,
    'high-emphasis-opacity': 0.95,
    'medium-emphasis-opacity': 0.65,
    'disabled-opacity': 0.35,
  },
};

const mereyLight: ThemeDefinition = {
  dark: false,
  colors: {
    // 1. Fondos y capas (Gama Slate / Zinc)
    background: '#F1F5F9',        // Slate 100: fondo general de la app (se nota que no es blanco)
    surface: '#FFFFFF',           // Blanco puro: tarjetas, modales y dropdowns destacan con claridad
    'surface-bright': '#FFFFFF',
    'surface-light': '#F8FAFC',   // Slate 50: ideal para sidebars o paneles laterales
    'surface-variant': '#E2E8F0', // Slate 200: chips, badges, hover de items
    'on-surface-variant': '#475569', // Texto secundario / iconos neutrales

    // 2. Colores de acento con contraste accesible en fondo blanco
    primary: '#0284C7',           // Sky 600 (contraste óptimo)
    'primary-darken-1': '#0369A1',
    secondary: '#4F46E5',         // Indigo 600
    'secondary-darken-1': '#4338CA',

    // 3. Estados
    error: '#DC2626',             // Red 600
    info: '#0284C7',
    success: '#16A34A',           // Green 600
    warning: '#D97706',           // Amber 600
  },
  variables: {
    // Bordes sutiles pero visibles (Slate 900 con 8-10% opacidad)
    'border-color': '#0F172A',
    'border-opacity': 0.08,

    // Jerarquía tipográfica
    'high-emphasis-opacity': 0.90,    // Texto principal casi negro (#0f172a feeling)
    'medium-emphasis-opacity': 0.65,  // Subtítulos y metadatos (#475569)
    'disabled-opacity': 0.38,
    'hover-opacity': 0.05,
    'selected-opacity': 0.08,
  },
};

export default createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'mereyDark',
    themes: {
      mereyDark,
      mereyLight,
    },
  },
});