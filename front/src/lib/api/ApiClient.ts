import { ApiClientError } from '@/lib/api/ApiClientError';
import { handleApiError } from '@/lib/api/handleApiError';
import { GameResource } from '@/lib/api/resources/GameResource';
import { CookieRegistryInterface } from '@/lib/cookie/CookieRegistryInterface';
import type { User } from '@/utils/types';
import { apiPaths } from './paths';
import { MeResource } from './resources/MeResource';
import { UserResource } from './resources/UserResource';
import { TempUserResource } from './resources/TempUserResource';
import { PlayerResource } from './resources/PlayerResource';

export interface LoginResponse {
  token: string;
  refresh_token: string;
  user?: User;
}

export interface RefreshResponse {
  token: string;
  refresh_token: string;
}

export interface DeleteResponse {
  success: boolean;
}

export interface BasicActionResponse {
  success: boolean;
}

export interface CollectionResponse<T> {
  member: T[];
  totalItems: number;
  view: {
    '@id': string;
    '@type': string;
    first: string;
    last: string;
    next: string;
  };
}

export enum ResponseType {
  RAW = 'raw',
  JSON = 'json',
}

export class ApiClient {
  token: string | null = null;
  refreshToken: string | null = null;

  me: MeResource;
  user: UserResource;
  tempUser: TempUserResource;
  player: PlayerResource;
  game: GameResource;

  constructor(
    public baseUrl: string,
    public cookieRegistry: CookieRegistryInterface
  ) {
    this.me = new MeResource(this);
    this.user = new UserResource(this);
    this.tempUser = new TempUserResource(this);
    this.player = new PlayerResource(this);
    this.game = new GameResource(this);
  }

  private async refreshAndRetryOn401<T>(error: ApiClientError, callback: () => Promise<T>) {
    if (error.code === 401 && error.message.includes('JWT Token')) {
      const refreshResponse = await this.refresh();
      if (refreshResponse instanceof ApiClientError) {
        return error;
      }

      return callback();
    }

    return error;
  }

  public async retrieveTokens() {
    this.token = await this.cookieRegistry.getCookie('token');
    this.refreshToken = await this.cookieRegistry.getCookie('refresh_token');

    return this;
  }

  public async setTokens(token: string, refreshToken: string) {
    this.token = token;
    this.refreshToken = refreshToken;

    const decodedTokenExp: number = JSON.parse(atob(token.split('.')[1]))?.exp ?? 0;
    this.cookieRegistry.setCookie('token', token, new Date(decodedTokenExp * 1000));
    this.cookieRegistry.setCookie('refresh_token', refreshToken, new Date(new Date().getTime() + 2592000));
  }

  async get<T>(
    url: string,
    additionnalHeaders: HeadersInit = {},
    autoRefresh: boolean = true
  ): Promise<T | ApiClientError> {
    return fetch(`${this.baseUrl}${url}`, {
      headers: {
        Accept: 'application/json',
        ...additionnalHeaders,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
    })
      .then(handleApiError)
      .then((response) => response.json())
      .catch((error: ApiClientError) => {
        if (autoRefresh) {
          return this.refreshAndRetryOn401(error, () => this.get(url, additionnalHeaders));
        }

        return error;
      });
  }

  async post<T>(
    url: string,
    body: object = {},
    additionnalHeaders: HeadersInit = {},
    responseType: ResponseType = ResponseType.JSON,
    autoRefresh: boolean = true
  ): Promise<T | ApiClientError> {
    const isFormData = body instanceof FormData;

    const headers: HeadersInit = isFormData
      ? {
          Accept: 'application/json',
          ...additionnalHeaders,
        }
      : {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          ...additionnalHeaders,
        };

    return fetch(`${this.baseUrl}${url}`, {
      method: 'POST',
      headers: {
        ...headers,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
      body: isFormData ? body : JSON.stringify(body),
    })
      .then(handleApiError)
      .then((response) => (responseType === ResponseType.RAW ? response : response.json()))
      .catch((error: ApiClientError) => {
        if (autoRefresh) {
          return this.refreshAndRetryOn401(error, () => this.post(url, body, additionnalHeaders, responseType));
        }

        return error;
      });
  }

  async patch<T>(
    url: string,
    body: object = {},
    additionnalHeaders: HeadersInit = {},
    autoRefresh: boolean = true
  ): Promise<T | ApiClientError> {
    const headers: HeadersInit = {
      Accept: 'application/json',
      'Content-Type': 'application/merge-patch+json',
      ...additionnalHeaders,
    };

    return fetch(`${this.baseUrl}${url}`, {
      method: 'PATCH',
      headers: {
        ...headers,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
      body: JSON.stringify(body),
    })
      .then(handleApiError)
      .then((response) => response.json())
      .catch((error: ApiClientError) => {
        if (autoRefresh) {
          return this.refreshAndRetryOn401(error, () => this.patch(url, body, additionnalHeaders));
        }

        return error;
      });
  }

  public async put<T>(
    url: string,
    body: object = {},
    additionnalHeaders: HeadersInit = {},
    autoRefresh: boolean = true
  ): Promise<T | ApiClientError> {
    const isFormData = body instanceof FormData;

    const headers: HeadersInit = isFormData
      ? {
          Accept: 'application/json',
          ...additionnalHeaders,
        }
      : {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          ...additionnalHeaders,
        };

    return fetch(`${this.baseUrl}${url}`, {
      method: 'PUT',
      headers: {
        ...headers,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
      body: isFormData ? body : JSON.stringify(body),
    })
      .then(handleApiError)
      .then((response) => response.json())
      .catch((error: ApiClientError) => {
        if (autoRefresh) {
          return this.refreshAndRetryOn401(error, () => this.put(url, body, additionnalHeaders));
        }

        return error;
      });
  }

  async delete(url: string, autoRefresh: boolean = true): Promise<DeleteResponse | ApiClientError> {
    return fetch(`${this.baseUrl}${url}`, {
      method: 'DELETE',
      headers: {
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
    })
      .then(handleApiError)
      .then((response) => ({ success: response.status === 204 }))
      .catch((error: ApiClientError) => {
        if (autoRefresh) {
          return this.refreshAndRetryOn401(error, () => this.delete(url));
        }

        return error;
      });
  }

  async login(email: string, password: string): Promise<LoginResponse | ApiClientError> {
    return this.post<LoginResponse>(apiPaths.login, {
      username: email,
      password,
    })
      .then((response) => {
        if (!(response instanceof ApiClientError) && response.token) {
          this.setTokens(response.token, response.refresh_token);
        }

        return response;
      })
      .then(async (response) => {
        if (!(response instanceof ApiClientError) && response.token) {
          const maybeUser = await this.me.get();
          if (maybeUser instanceof ApiClientError) {
            return maybeUser;
          }

          return {
            ...response,
            user: maybeUser,
          };
        }

        return response;
      });
  }

  async refresh(): Promise<RefreshResponse | ApiClientError> {
    return this.post<RefreshResponse>(
      apiPaths.refreshToken,
      { refresh_token: this.refreshToken },
      {},
      ResponseType.JSON,
      false
    ).then((response) => {
      console.log(this.refreshToken);
      if (!(response instanceof ApiClientError) && response.token) {
        this.setTokens(response.token, response.refresh_token);
      }

      return response;
    });
  }

  logout(): void {
    this.token = null;
    this.cookieRegistry.eraseCookie('token');

    this.refreshToken = null;
    this.cookieRegistry.eraseCookie('refresh_token');
  }
}
