import { ApiClient } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';

export interface GetTempUserResponse {
  token: string;
  refreshToken: string;
}

export class TempUserResource {
  constructor(private apiClient: ApiClient) {}

  public async get(username: string): Promise<GetTempUserResponse | ApiClientError> {
    return this.apiClient.get<GetTempUserResponse>(apiPaths.tempUser.get(username));
  }
}
