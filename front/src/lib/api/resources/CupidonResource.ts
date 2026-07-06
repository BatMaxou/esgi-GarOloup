import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';

export class CupidonResource {
  constructor(private apiClient: ApiClient) {}

  public async setup(firstLoverId: string, secondLoverId: string): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.cupidon.setup, { firstLoverId, secondLoverId });
  }
}
