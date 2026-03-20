import { credentialsClient, defaultCredentialsSchema } from "better-auth-credentials-plugin/client"
import { inferAdditionalFields } from "better-auth/client/plugins"
import { createAuthClient } from "better-auth/react"
import { User as BetterAuthUser } from "better-auth/types"

import type { User } from "@/utils/types"
import { auth } from "@/lib/auth"

export const { signIn, signUp, signOut, useSession, updateUser, ...authClient } = createAuthClient({
  plugins: [
    credentialsClient<User & BetterAuthUser, "/sign-in/garoloup", typeof defaultCredentialsSchema>(),
    inferAdditionalFields<typeof auth>(),
  ],
})
