import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import type { Configuration, Game, RoleDispatchEntry } from '@/utils/types';

export interface CreateGameResponse {
  joinCode: string;
}

export class GameResource {
  constructor(private apiClient: ApiClient) {}

  public async getCurrent(): Promise<Game | ApiClientError> {
    return this.apiClient.get<Game>(apiPaths.game.getCurrent);
  }

  public async create(): Promise<CreateGameResponse | ApiClientError> {
    return this.apiClient.post<CreateGameResponse>(apiPaths.game.create);
  }

  public async join(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.post<BasicActionResponse>(apiPaths.game.join);
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
}
