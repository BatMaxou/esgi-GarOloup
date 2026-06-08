import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';

export class WitchResource {
  constructor(private apiClient: ApiClient) {}

  public async save(targetPlayerId: string): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.witch.save, { targetPlayerId });
  }

  public async poison(targetPlayerId: string): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.witch.poison, { targetPlayerId });
  }
}
