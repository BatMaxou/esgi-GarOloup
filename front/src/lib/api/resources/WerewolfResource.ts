import { ApiClient } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import type { WerewolfTeam } from '@/utils/types';

export class WerewolfResource {
  constructor(private apiClient: ApiClient) {}

  public async getTeam(): Promise<WerewolfTeam | ApiClientError> {
    return this.apiClient.get<WerewolfTeam>(apiPaths.werewolf.team);
  }
}
