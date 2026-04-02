import { ApiClient } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import { GameTeamEnum } from '@/utils/enums';

export class FilterResource {
  constructor(private apiClient: ApiClient) {}

  public async getGameTeamFilters(): Promise<GameTeamEnum[] | ApiClientError> {
    return this.apiClient.get<GameTeamEnum[]>(apiPaths.filter.gameTeam);
  }
}
