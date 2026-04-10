import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import type { Configuration, Game, RoleDispatchEntry } from '@/utils/types';

export interface CreateGamePayload {
  maxPlayers: number;
  maxTimeForDiscussion: number;
  public: boolean;
}

export interface CreateGameResponse {
  joinCode: string;
}

export interface JoinGamePayload {
  joinCode: string;
}

export class GameResource {
  constructor(private apiClient: ApiClient) {}

  public async getCurrent(): Promise<Game | ApiClientError> {
    return this.apiClient.get<Game>(apiPaths.game.getCurrent);
  }

  public async create(payload: CreateGamePayload): Promise<CreateGameResponse | ApiClientError> {
    return this.apiClient.post<CreateGameResponse>(apiPaths.game.create, payload);
  }

  public async join(payload: JoinGamePayload): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.post<BasicActionResponse>(apiPaths.game.join, payload);
  }

  public async close(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.close);
  }

  public async open(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.open);
  }

  public async setConfiguration(configuration: Configuration): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.setConfiguration, { configuration });
  }

  public async dispatchRoles(dispatch: RoleDispatchEntry[]): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.roleDispatch, { dispatch });
  }

  public async launch(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.launch);
  }
}
