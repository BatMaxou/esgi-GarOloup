import { ApiClient } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import type { User } from '@/utils/types';

export class MeResource {
  constructor(private apiClient: ApiClient) {}

  async get(): Promise<User | ApiClientError> {
    return this.apiClient.get<User>(apiPaths.user.me);
  }
}
