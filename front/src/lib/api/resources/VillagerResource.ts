import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';

export class VillagerResource {
  constructor(private apiClient: ApiClient) {}

  public async setup(targetPlayerId: string): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.villager.setup, { targetPlayerId });
  }
}
