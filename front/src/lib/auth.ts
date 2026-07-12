import { betterAuth } from 'better-auth';

import { providers } from '@/lib/auth/providers';
import { plugins } from '@/lib/auth/auth-plugin';
import { userAdditionalFields } from '@/lib/auth/user-fields';

export const auth = betterAuth({
  session: {
    expiresIn: 60 * 60 * 24 * 7,
    cookieCache: {
      version: 'v2',
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
    additionalFields: userAdditionalFields,
  },
  ...providers,
  plugins: [...plugins],
});
