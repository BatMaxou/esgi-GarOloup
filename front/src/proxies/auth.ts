import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

import { apiBaseUrl } from '@/utils/tools';

export async function authProxy(request: NextRequest) {
  const token = request.cookies.get('token')?.value;
  const refreshToken = request.cookies.get('refresh_token')?.value;

  if (!token && !refreshToken) {
    return NextResponse.redirect(new URL('/', request.url));
  }

  if (!token && refreshToken) {
    try {
      const response = await fetch(`${apiBaseUrl}/token/refresh`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ refresh_token: refreshToken }),
      });

      if (!response.ok) {
        const nextResponse = NextResponse.redirect(new URL('/login', request.url));
        nextResponse.cookies.delete('refresh_token');

        return nextResponse;
      }

      const { token: newToken, refresh_token: newRefreshToken } = await response.json();
      const nextResponse = NextResponse.next();

      nextResponse.cookies.set('token', newToken);
      nextResponse.cookies.set('refresh_token', newRefreshToken);

      return nextResponse;
    } catch {
      return NextResponse.redirect(new URL('/login', request.url));
    }
  }

  return NextResponse.next();
}

export const authMatcher = [
  '/game',
  // put here account paths
  // or other game paths if needed
];
