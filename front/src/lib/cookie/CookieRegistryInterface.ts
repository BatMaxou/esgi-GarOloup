export interface CookieRegistryInterface {
  getCookie: (name: string) => Promise<string | null>;
  setCookie: (name: string, value: string, expire?: Date|null) => Promise<void>;
  eraseCookie: (name: string) => Promise<void>;
}

