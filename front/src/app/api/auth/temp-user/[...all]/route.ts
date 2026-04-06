import { toNextJsHandler } from 'better-auth/next-js';

import { tempUserAuth } from '@/lib/auth/temp-user/server';

export const { POST, GET } = toNextJsHandler(tempUserAuth);
