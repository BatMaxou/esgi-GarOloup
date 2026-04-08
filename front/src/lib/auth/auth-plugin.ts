import { nextCookies } from 'better-auth/next-js';

import { credentialsPlugin } from '@/lib/auth/plugins/credentials';

export const plugins = [
  credentialsPlugin,
  nextCookies(), // Must be the last plugin
];
