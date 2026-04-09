import { NextRequest, NextResponse } from 'next/server';
import { getSessionCookie } from 'better-auth/cookies';
import createMiddleware from 'next-intl/middleware';

import { routing } from '@/i18n/routing';
import { paths } from '@/utils/paths';

const protectedRoutes = ['/game'];

const isProtected = (pathname: string) =>
  protectedRoutes.some((route) => pathname === route || pathname.startsWith(`${route}/`));

export const proxy = (request: NextRequest) => {
  const { pathname } = request.nextUrl;

  if (isProtected(pathname)) {
    const mainSession = getSessionCookie(request);
    const tempSession = getSessionCookie(request, { cookiePrefix: 'better-auth-temp-user' });

    if (!mainSession && !tempSession) {
      return NextResponse.redirect(new URL(paths.login, request.url));
    }
  }

  return createMiddleware(routing)(request);
};

export const config = {
  matcher: '/((?!_next|favicon\\.ico|api/|.*\\.[a-zA-Z0-9]+$).*)',
};
