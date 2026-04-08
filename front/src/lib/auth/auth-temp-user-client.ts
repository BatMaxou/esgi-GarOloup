'use client';

import { credentialsClient } from 'better-auth-credentials-plugin/client';
import { createAuthClient } from 'better-auth/react';
import { User as BetterAuthUser } from 'better-auth/types';

import type { User } from '@/utils/types';
import { tempUserCredentialsSchema } from '@/lib/auth/plugins/temp-user-credentials-schema';

export const {
  signIn: tempUserSignIn,
  signOut: tempUserSignOut,
  useSession: useTempUserSession,
  updateUser: tempUserUpdateUser,
  ...tempUserAuthClient
} = createAuthClient({
  basePath: '/api/auth/temp-user',
  plugins: [credentialsClient<User & BetterAuthUser, '/sign-in/temp-user', typeof tempUserCredentialsSchema>()],
});
