import { betterAuth } from 'better-auth';
import { nextCookies } from 'better-auth/next-js';

import { providers } from '@/lib/auth/providers';
import { tempUserCredentialsPlugin } from '@/lib/auth/plugins/temp-user-credentials';

export const tempUserAuth = betterAuth({
  basePath: '/api/auth/temp-user',
  advanced: {
    cookiePrefix: 'better-auth-temp-user',
  },
  session: {
    expiresIn: 60 * 60 * 24 * 7,
    cookieCache: {
      version: 'v1',
      enabled: true,
      maxAge: 60 * 60 * 24 * 7,
      strategy: 'jwt',
      refreshCache: true,
    },
  },
  account: {
    storeStateStrategy: 'cookie',
    storeAccountCookie: true,
  },
  user: {
    additionalFields: {
      token: {
        type: 'string',
        returned: true,
        required: false,
      },
      refreshToken: {
        type: 'string',
        returned: true,
        required: false,
      },
    },
  },
  ...providers,
  plugins: [tempUserCredentialsPlugin, nextCookies()],
});
