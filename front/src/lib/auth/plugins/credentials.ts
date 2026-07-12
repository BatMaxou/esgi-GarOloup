import { credentials } from 'better-auth-credentials-plugin';
import { User as BetterAuthUser } from 'better-auth/types';
import { z } from 'zod';

import { User } from '@/utils/types';
import { getApiClient } from '@/utils/server/clients';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { mapApiUserToAuthUser } from '@/lib/auth/user-fields';

export const credentialsPlugin = credentials({
  autoSignUp: true,
  providerId: 'garoloup-api',
  path: '/sign-in/garoloup',
  inputSchema: z.object({
    email: z.string().email(),
    password: z.string(),
  }),
  UserType: {} as BetterAuthUser & User,
  callback: async (ctx, parsed) => {
    const { email, password } = parsed;

    const apiClient = await getApiClient();

    const maybeLoginResponse = await apiClient.login(email, password);
    if (maybeLoginResponse instanceof ApiClientError) {
      throw maybeLoginResponse;
    }

    const { token, refresh_token: refreshToken } = maybeLoginResponse;
    apiClient.setTokens(token, refreshToken, false);

    const maybeMeResponse = await apiClient.me.get();
    if (maybeMeResponse instanceof ApiClientError) {
      throw maybeMeResponse;
    }

    const user = maybeMeResponse;

    return {
      ...mapApiUserToAuthUser(user, email),
      token,
      refreshToken,
    };
  },
});
// handle temp user here with another credentials plugin ?
