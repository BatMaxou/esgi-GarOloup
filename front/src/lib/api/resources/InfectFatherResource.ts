import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';

export class InfectFatherResource {
  constructor(private apiClient: ApiClient) {}

  public async infect(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.infectFather.infect);
  }
}
