import type { NextRequest } from 'next/server';

import { authProxy, authMatcher } from '@/proxies/auth';

const routes: Array<{
  matcher: string[];
  handler: (request: NextRequest) => Promise<Response>;
}> = [{ matcher: authMatcher, handler: authProxy }];

export function proxy(request: NextRequest) {
  const { pathname } = request.nextUrl;

  for (const route of routes) {
    const matches = route.matcher.some((pattern) => {
      const regex = new RegExp(`^${pattern.replace(':path*', '.*')}$`);

      return regex.test(pathname);
    });

    if (matches) {
      return route.handler(request);
    }
  }
}

export const config = {
  matcher: '/((?!_next|favicon.ico).*)',
};
