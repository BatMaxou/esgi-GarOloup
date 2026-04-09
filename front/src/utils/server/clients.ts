'use server';

import { headers } from 'next/headers';

import { auth } from '@/lib/auth';
import { apiBaseUrl } from '@/utils/tools';
import { ApiClient } from '@/lib/api/ApiClient';
import { tempUserAuth } from '@/lib/auth/temp-user/server';

export const getSession = async (): Promise<ReturnType<typeof auth.api.getSession> | ReturnType<typeof tempUserAuth.api.getSession>> => {
  const session = await auth.api.getSession({
    headers: await headers(),
  });
  const tempUserSession = await tempUserAuth.api.getSession({
    headers: await headers(),
  });
  return session ?? tempUserSession
};

export const getApiClient = async (): Promise<ApiClient> => {
  const session = await getSession();

  return new ApiClient(apiBaseUrl, session?.user?.token, session?.user?.refreshToken);
};
