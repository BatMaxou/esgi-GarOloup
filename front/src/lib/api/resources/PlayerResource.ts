import { ApiClient, DeleteResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import type { Player } from '@/utils/types';

export class PlayerResource {
  constructor(private apiClient: ApiClient) {}

  public async getCurrent(): Promise<Player | ApiClientError> {
    return this.apiClient.get<Player>(apiPaths.player.getCurrent);
  }

  public async leave(): Promise<DeleteResponse | ApiClientError> {
    return this.apiClient.delete(apiPaths.player.leave);
  }
}
