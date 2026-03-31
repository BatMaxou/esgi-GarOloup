import { pathnames } from '@/i18n/pathnames';

export const paths: Record<string, keyof typeof pathnames> = {
  home: '/',
  game: '/game',
  ui: '/ui',
  formUi: '/form-ui',
  icons: '/icons',
  login: '/login',
  register: '/register',
  forgotPassword: '/forgot-password',
  resetPassword: '/reset-password',
  test: '/test',
};
