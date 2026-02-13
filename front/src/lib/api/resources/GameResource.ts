import { ApiClient } from "@/lib/api/ApiClient";
import { ApiClientError } from "@/lib/api/ApiClientError";
import { apiPaths } from "@/lib/api/paths";
import { Game } from "@/utils/types";

export class GameResource {
  constructor(private apiClient: ApiClient) {}

  public async get(id: string): Promise<Game | ApiClientError> {
    return this.apiClient.get<Game>(apiPaths.game.get(id));
  }

  public async update(id: string, data: Partial<Game>): Promise<Game | ApiClientError> {
    return this.apiClient.patch<Game>(apiPaths.game.update(id), data);
  }
}

