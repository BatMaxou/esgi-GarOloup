'use server';

import { headers } from 'next/headers';

import { auth } from '@/lib/auth';
import { apiBaseUrl } from '@/utils/tools';
import { ApiClient } from '@/lib/api/ApiClient';

export const getSession = async (): Promise<ReturnType<typeof auth.api.getSession>> => {
  return await auth.api.getSession({
    headers: await headers(),
  });
};

export const getApiClient = async (): Promise<ApiClient> => {
  const session = await getSession();

  return new ApiClient(apiBaseUrl, session?.user?.token, session?.user?.refreshToken);
};
