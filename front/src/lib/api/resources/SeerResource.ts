import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';

export class SeerResource {
  constructor(private apiClient: ApiClient) {}

  public async reveal(targetPlayerId: string): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.seer.reveal, { targetPlayerId });
  }
}
