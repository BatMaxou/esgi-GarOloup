import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import type { Game } from '@/utils/types';

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
    return this.apiClient.post<BasicActionResponse>(apiPaths.game.close);
  }
}
