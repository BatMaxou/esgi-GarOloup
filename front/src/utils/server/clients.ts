'use server';

import { ApiClient } from '@/lib/api/ApiClient';
import { apiBaseUrl } from '@/utils/tools';
import { ServerCookieRegistry } from '@/lib/cookie/ServerCookieRegistry';

export const getApiClient = async () => await new ApiClient(apiBaseUrl, new ServerCookieRegistry()).retrieveTokens();
