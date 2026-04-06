import { credentials } from 'better-auth-credentials-plugin';
import { User as BetterAuthUser } from 'better-auth/types';

import { User } from '@/utils/types';
import { getApiClient } from '@/utils/server/clients';
import { ApiClientError } from '@/lib/api/ApiClientError';

import { tempUserCredentialsSchema } from '@/lib/auth/plugins/temp-user-credentials-schema';

export const tempUserCredentialsPlugin = credentials({
  autoSignUp: true,
  providerId: 'temp-user-api',
  path: '/sign-in/temp-user',
  inputSchema: tempUserCredentialsSchema,
  UserType: {} as BetterAuthUser & User,
  callback: async (ctx, parsed) => {
    const { username } = parsed;

    const apiClient = await getApiClient();

    const maybeTempUserResponse = await apiClient.tempUser.get(username);
    if (maybeTempUserResponse instanceof ApiClientError) {
      throw maybeTempUserResponse;
    }

    const { token, refreshToken } = maybeTempUserResponse;
    apiClient.setTokens(token, refreshToken, false);

    const maybeMeResponse = await apiClient.me.get();
    if (maybeMeResponse instanceof ApiClientError) {
      throw maybeMeResponse;
    }

    const user = maybeMeResponse;
    const email = user.email?.trim() || `${username.toLowerCase()}@temp.garoloup`;

    return {
      ...user,
      email,
      token,
      refreshToken,
    };
  },
});
