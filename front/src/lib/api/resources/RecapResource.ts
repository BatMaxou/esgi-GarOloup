import { ApiClient } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import { Recap } from '@/utils/types';

export class RecapResource {
  constructor(private apiClient: ApiClient) {}

  public async getByGame(gameId: string): Promise<Recap | ApiClientError> {
    return this.apiClient.get<Recap>(apiPaths.recap.getByGame(gameId));
  }
}
