import { ApiClientError } from "@/lib/api/ApiClientError";
import { handleApiError } from "@/lib/api/handleApiError";
import { GameResource } from "@/lib/api/resources/GameResource";
import { CookieRegistryInterface } from "@/lib/cookie/CookieRegistryInterface";
// import type { User } from "@/utils/types";

export interface LoginResponse {
  token: string;
  user?: unknown;
}

export interface DeleteResponse {
  success: boolean;
}

export interface CollectionResponse<T> {
  member: T[];
  totalItems: number;
  view: {
    "@id": string;
    "@type": string;
    first: string;
    last: string;
    next: string;
  };
}

export enum ResponseType {
  RAW = "raw",
  JSON = "json",
}

export class ApiClient {
  token: string | null = null;

  game: GameResource;

  constructor(
    public baseUrl: string,
    public cookieRegistry: CookieRegistryInterface,
  ) {
    this.retrieveToken();

    this.game = new GameResource(this);
  }

  private async retrieveToken() {
    this.token = await this.cookieRegistry.getCookie("token");
  }

  async get<T>(url: string, additionnalHeaders: HeadersInit = {}): Promise<T | ApiClientError> {
    return fetch(`${this.baseUrl}${url}`, {
      headers: {
        Accept: "application/json",
        ...additionnalHeaders,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
    })
      .then(handleApiError)
      .then((response) => response.json())
      .catch((error: ApiClientError) => error);
  }

  async post<T>(
    url: string,
    body: object = {},
    additionnalHeaders: HeadersInit = {},
    responseType: ResponseType = ResponseType.JSON,
  ): Promise<T | ApiClientError> {
    const isFormData = body instanceof FormData;

    const headers: HeadersInit = isFormData
      ? {
          Accept: "application/json",
          ...additionnalHeaders,
        }
      : {
          Accept: "application/json",
          "Content-Type": "application/json",
          ...additionnalHeaders,
        };

    return fetch(`${this.baseUrl}${url}`, {
      method: "POST",
      headers: {
        ...headers,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
      body: isFormData ? body : JSON.stringify(body),
    })
      .then(handleApiError)
      .then((response) => (responseType === ResponseType.RAW ? response : response.json()))
      .catch((error: ApiClientError) => error);
  }

  async patch<T>(url: string, body: object, additionnalHeaders: HeadersInit = {}): Promise<T | ApiClientError> {
    const headers: HeadersInit = {
      Accept: "application/json",
      "Content-Type": "application/merge-patch+json",
      ...additionnalHeaders,
    };

    return fetch(`${this.baseUrl}${url}`, {
      method: "PATCH",
      headers: {
        ...headers,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
      body: JSON.stringify(body),
    })
      .then(handleApiError)
      .then((response) => response.json())
      .catch((error: ApiClientError) => error);
  }

  public async put<T>(url: string, body: object = {}, additionnalHeaders: HeadersInit = {}): Promise<T | ApiClientError> {
    const isFormData = body instanceof FormData;

    const headers: HeadersInit = isFormData
      ? {
          Accept: "application/json",
          ...additionnalHeaders,
        }
      : {
          Accept: "application/json",
          "Content-Type": "application/json",
          ...additionnalHeaders,
        };

    return fetch(`${this.baseUrl}${url}`, {
      method: "PUT",
      headers: {
        ...headers,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
      body: isFormData ? body : JSON.stringify(body),
    })
      .then(handleApiError)
      .then((response) => response.json())
      .catch((error: ApiClientError) => error);
  }

  async delete(url: string): Promise<DeleteResponse | ApiClientError> {
    return fetch(`${this.baseUrl}${url}`, {
      method: "DELETE",
      headers: {
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
    })
      .then(handleApiError)
      .then((response) => ({ success: response.status === 204 }))
      .catch((error: ApiClientError) => error);
  }

  async login(email: string, password: string): Promise<LoginResponse | ApiClientError> {
    return this.post<LoginResponse>("/login", { username: email, password })
      .then((response) => {
        if (!(response instanceof ApiClientError) && response.token) {
          this.token = response.token;

          const decodedTokenExp: number = JSON.parse(atob(response.token.split(".")[1]))?.exp ?? 0;
          this.cookieRegistry.setCookie("token", response.token, new Date(decodedTokenExp * 1000));
        }

        return response;
      })
      .then(async (response) => {
        if (!(response instanceof ApiClientError) && response.token) {
          return {
            ...response,
            // user: await this.me.get().then((user) => user),
          };
        }

        return response;
      })
  }

  logout(): void {
    this.token = null;
    this.cookieRegistry.eraseCookie("token");
  }
}

