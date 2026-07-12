import { nextCookies } from 'better-auth/next-js';
import { customSession } from 'better-auth/plugins';

import { credentialsPlugin } from '@/lib/auth/plugins/credentials';
import { normalizeRoles } from '@/lib/auth/user-fields';

export const plugins = [
  credentialsPlugin,
  customSession(async ({ user, session }) => {
    const authUser = user as { token?: string | null; refreshToken?: string | null; roles?: unknown };
    return {
      user: {
        ...user,
        token: authUser.token ?? null,
        refreshToken: authUser.refreshToken ?? null,
        roles: normalizeRoles(authUser.roles),
      },
      session,
    };
  }),
  nextCookies(), // Must be the last plugin
];
