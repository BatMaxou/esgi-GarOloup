import { cookies } from "next/headers"

import { CookieRegistryInterface } from "@/lib/cookie/CookieRegistryInterface";

// @see https://nextjs.org/docs/app/api-reference/functions/cookies
// if cookies have to be set on server side, consider using Server Function

export class ServerCookieRegistry implements CookieRegistryInterface {
  async getCookie(name: string) {
    const cookieStore = await cookies();
    const cookie = cookieStore.get(name);

    if (cookie === undefined) {
      return null
    }

    return cookie.value;
  }

  async setCookie() {
    console.log('Cookies can not be set on server side');
  }

  async eraseCookie() {
    console.log('Cookies can not be erased on server side');
  }
}
