export class ClientCookieRegistry {
  async getCookie(name: string) {
    if (typeof document === 'undefined') {
      return null;
    }

    const cookies = document.cookie.split('; ');
    const value = cookies.find((cookie) => cookie.startsWith(`${name}=`))?.split('=')[1];

    if (value === undefined) {
      return null;
    }

    return decodeURIComponent(value);
  }

  async setCookie(name: string, value: string, expire?: Date | null, path?: string) {
    if (typeof document === 'undefined') {
      return;
    }

    document.cookie = `${name}=${encodeURIComponent(value)}; path=${path || '/'}; SameSite=Strict; Secure; ${expire ? `expires=${expire.toUTCString()};` : ''}`;
  }

  async eraseCookie(name: string) {
    if (typeof document === 'undefined') {
      return;
    }

    document.cookie = `${name}=; Max-Age=0; path=/`;
  }
}
