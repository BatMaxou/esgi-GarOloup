import { pathnames } from '@/i18n/pathnames';

export const loggedAreaPaths = { lobby: '/lobby' } as const;

export function isLoggedAreaPath(pathname: string): boolean {
  const normalized = (pathname.replace(/\/$/, '') || '/') as `/${string}` | '/';
  return (Object.values(loggedAreaPaths) as readonly string[]).some(
    (p) => normalized === p || normalized.startsWith(`${p}/`)
  );
}

export const paths = {
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
  admin: '/admin',
  adminRoles: '/admin/roles',
  adminRolesCreate: '/admin/roles/create',
  adminRolesEdit: '/admin/roles/[roleId]/edit',
  roleDetails: '/roles/[roleRef]',
  recap: '/[gameId]/recap',
  ...loggedAreaPaths,
} as const satisfies Record<string, keyof typeof pathnames>;
