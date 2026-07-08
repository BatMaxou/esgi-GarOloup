import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';

export class AssassinResource {
  constructor(private apiClient: ApiClient) {}

  public async kill(targetPlayerId: string): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.assassin.kill, { targetPlayerId });
  }
}
