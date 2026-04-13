import { ApiClient } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import { Homepage } from '@/utils/types';

export class HomepageResource {
  constructor(private apiClient: ApiClient) {}

  async get(): Promise<Homepage | ApiClientError> {
    return this.apiClient.get<Homepage>(apiPaths.homepage);
  }
}
