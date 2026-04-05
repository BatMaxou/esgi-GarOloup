import { ApiClient } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import { MercureToken } from '@/utils/types';

export class MercureResource {
  constructor(private apiClient: ApiClient) {}

  public async getToken(): Promise<MercureToken | ApiClientError> {
    return this.apiClient.get<MercureToken>(apiPaths.mercure.credentials);
  }
}
