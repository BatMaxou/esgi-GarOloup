import { NextRequest, NextResponse } from 'next/server';
import { getSessionCookie } from 'better-auth/cookies';
import { paths } from './utils/paths';

const protectedRoutes = ['/game'];

const isProtected = (pathname: string) =>
  protectedRoutes.some((route) => pathname === route || pathname.startsWith(`${route}/`));

export const proxy = (request: NextRequest) => {
  const { pathname } = request.nextUrl;

  if (isProtected(pathname)) {
    const sessionCookie = getSessionCookie(request);

    if (!sessionCookie) {
      return NextResponse.redirect(new URL(paths.login, request.url));
    }
  }

  return NextResponse.next();
};

export const config = {
  matcher: '/((?!_next|favicon.ico|images|api|logo).*)',
};
