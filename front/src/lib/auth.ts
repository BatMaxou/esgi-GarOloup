import { betterAuth } from "better-auth";

import { providers } from "@/lib/auth/providers";
import { plugins } from "@/lib/auth/plugins";

export const auth = betterAuth({
  session: {
    expiresIn: 60 * 60 * 24 * 30,
    updateAge: 60 * 60 * 24,
    freshAge: 60 * 60,
    cookieCache: {
      version: 'v1',
      enabled: true,
      maxAge: 60 * 60 * 24 * 30,
      strategy: "jwt",
      refreshCache: true,
    },
  },
  account: {
    storeStateStrategy: "cookie",
    storeAccountCookie: true,
  },
  user: {
    additionalFields: {
      token: {
        type: "string",
        returned: true,
        required: false
      },
      refreshToken: {
        type: "string",
        returned: true,
        required: false
      }
    }
  },
  ...providers,
  plugins: [
    ...plugins,
  ],
});
