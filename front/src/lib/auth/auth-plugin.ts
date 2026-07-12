import { nextCookies } from 'better-auth/next-js';
import { customSession } from 'better-auth/plugins';

import { credentialsPlugin } from '@/lib/auth/plugins/credentials';
import { normalizeRoles } from '@/lib/auth/user-fields';

export const plugins = [
  credentialsPlugin,
  customSession(async ({ user, session }) => ({
    user: {
      ...user,
      roles: normalizeRoles((user as { roles?: unknown }).roles),
    },
    session,
  })),
  nextCookies(), // Must be the last plugin
];
