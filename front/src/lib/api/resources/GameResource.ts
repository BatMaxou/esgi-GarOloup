import { ApiClient, BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import { GameRoleEnum } from '@/utils/enums';
import type { Game, RoleDispatchEntry } from '@/utils/types';

export interface CreateGamePayload {
  maxPlayers: number;
  maxTimeForDiscussion: number;
  public: boolean;
}

export interface CreateGameResponse {
  joinCode: string;
}

export interface JoinGamePayload {
  joinCode: string;
}

export interface GameConfigurationPayload {
  composition: {
    roles: { role: GameRoleEnum; count: number }[];
  };
  withGameMaster: boolean;
  withRandomDispatch: boolean;
}

export class GameResource {
  constructor(private apiClient: ApiClient) {}

  public async getCurrent(): Promise<Game | ApiClientError> {
    return this.apiClient.get<Game>(apiPaths.game.getCurrent);
  }

  public async getPublics(page: number = 1, itemsPerPage?: number) {
    return this.apiClient.getCollection<Game>(apiPaths.game.getPublics(page, itemsPerPage));
  }

  public async create(payload: CreateGamePayload): Promise<CreateGameResponse | ApiClientError> {
    return this.apiClient.post<CreateGameResponse>(apiPaths.game.create, payload);
  }

  public async join(payload: JoinGamePayload): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.post<BasicActionResponse>(apiPaths.game.join, payload);
  }

  public async close(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.close);
  }

  public async open(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.open);
  }

  public async setConfiguration(
    configuration: GameConfigurationPayload
  ): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.setConfiguration, { ...configuration });
  }

  public async resetConfiguration(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.resetConfiguration);
  }

  public async setGameMaster(playerId: string): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.setGameMaster, { playerId });
  }

  public async resetGameMaster(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.resetGameMaster);
  }

  public async dispatchRoles(dispatch: RoleDispatchEntry[]): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.roleDispatch, { dispatch });
  }

  public async resetRoleDispatch(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.resetRoleDispatch);
  }

  public async launch(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.launch);
  }

  public async vote(targetPlayerId: string): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.vote, { targetPlayerId });
  }

  public async timeUp(): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.patch<BasicActionResponse>(apiPaths.game.timeUp);
  }
}
