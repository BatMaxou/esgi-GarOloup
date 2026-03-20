import { NextRequest, NextResponse } from 'next/server';
import { getSessionCookie } from 'better-auth/cookies';

const protectedRoutes = ['/game'];

export function proxy(request: NextRequest) {
  const { pathname } = request.nextUrl;

  const isProtected = protectedRoutes.some((route) => pathname === route || pathname.startsWith(`${route}/`));

  if (isProtected) {
    const sessionCookie = getSessionCookie(request);

    if (!sessionCookie) {
      return NextResponse.redirect(new URL('/login', request.url));
    }
  }

  return NextResponse.next();
}

export const config = {
  matcher: '/((?!_next|favicon.ico|images|api|logo).*)',
};
