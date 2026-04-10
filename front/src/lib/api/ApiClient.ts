import { ApiClientError } from '@/lib/api/ApiClientError';
import { handleApiError } from '@/lib/api/handleApiError';
import { GameResource } from '@/lib/api/resources/GameResource';
import { apiPaths } from '@/lib/api/paths';
import { MeResource } from '@/lib/api/resources/MeResource';
import { UserResource } from '@/lib/api/resources/UserResource';
import { TempUserResource } from '@/lib/api/resources/TempUserResource';
import { PlayerResource } from '@/lib/api/resources/PlayerResource';
import { RoleResource } from '@/lib/api/resources/RoleResource';
import { FilterResource } from '@/lib/api/resources/FilterResource';
import { MercureResource } from '@/lib/api/resources/MercureResource';

type PropagateChangeToken = (token?: string | null, refreshToken?: string | null) => void;

export interface LoginResponse {
  token: string;
  refresh_token: string;
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
  view?: {
    '@id': string;
    '@type': string;
    first: string;
    last: string;
    next?: string;
    previous?: string;
  };
}

export enum ResponseType {
  RAW = 'raw',
  JSON = 'json',
}

export class ApiClient {
  me: MeResource;
  user: UserResource;
  tempUser: TempUserResource;
  player: PlayerResource;
  game: GameResource;
  role: RoleResource;
  filter: FilterResource;
  mercure: MercureResource;

  constructor(
    public baseUrl: string,
    public token: string | null = null,
    public refreshToken: string | null = null,
    private propagateChangeToken: PropagateChangeToken = () => {}
  ) {
    this.me = new MeResource(this);
    this.user = new UserResource(this);
    this.tempUser = new TempUserResource(this);
    this.player = new PlayerResource(this);
    this.game = new GameResource(this);
    this.role = new RoleResource(this);
    this.filter = new FilterResource(this);
    this.mercure = new MercureResource(this);
  }

  public initPropagateChangeToken(propagateChangeToken: PropagateChangeToken) {
    this.propagateChangeToken = propagateChangeToken;
  }

  public async get<T>(
    url: string,
    additionnalHeaders: HeadersInit = {},
    autoRefresh: boolean = true
  ): Promise<T | ApiClientError> {
    return fetch(`${this.baseUrl}${url}`, {
      cache: 'no-store',
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

  public async getCollection<T>(
    url: string,
    additionnalHeaders: HeadersInit = {},
    autoRefresh: boolean = true
  ): Promise<CollectionResponse<T> | ApiClientError> {
    return fetch(`${this.baseUrl}${url}`, {
      cache: 'no-store',
      headers: {
        Accept: 'application/ld+json',
        ...additionnalHeaders,
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
      },
    })
      .then(handleApiError)
      .then((response) => response.json())
      .catch((error: ApiClientError) => {
        if (autoRefresh) {
          return this.refreshAndRetryOn401(error, () => this.getCollection(url, additionnalHeaders));
        }

        return error;
      });
  }

  public async post<T>(
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

  public async patch<T>(
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

  public async delete(url: string, autoRefresh: boolean = true): Promise<DeleteResponse | ApiClientError> {
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

  public async login(email: string, password: string): Promise<LoginResponse | ApiClientError> {
    return this.post<LoginResponse>(apiPaths.login, {
      username: email,
      password,
    }).then((response) => {
      if (!(response instanceof ApiClientError) && response.token) {
        this.setTokens(response.token, response.refresh_token);
      }

      return response;
    });
  }

  public setTokens(token?: string | null, refreshToken?: string | null, withPropagation: boolean = true) {
    this.token = token ?? null;
    this.refreshToken = refreshToken ?? null;

    if (!withPropagation) {
      return;
    }

    this.propagateChangeToken(token, refreshToken);
  }

  private async refresh(): Promise<RefreshResponse | ApiClientError> {
    return this.post<RefreshResponse>(
      apiPaths.refreshToken,
      { refresh_token: this.refreshToken },
      {},
      ResponseType.JSON,
      false
    ).then((response) => {
      if (!(response instanceof ApiClientError) && response.token) {
        this.setTokens(response.token, response.refresh_token);
      }
      return response;
    });
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
}
