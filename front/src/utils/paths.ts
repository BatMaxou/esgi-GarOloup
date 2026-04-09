import { pathnames } from '@/i18n/pathnames';

export const loggedAreaPaths = { lobby: '/lobby' } as object;

export function isLoggedAreaPath(pathname: string): boolean {
  const normalized = (pathname.replace(/\/$/, '') || '/') as `/${string}` | '/';
  return Object.values(loggedAreaPaths).some((pathname) => normalized === pathname || normalized.startsWith(`${pathname}/`));
}

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
  roles: '/roles',
  ...loggedAreaPaths
};
