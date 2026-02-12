import { ApiClient } from "@/lib/api/ApiClient";
import { ApiClientError } from "@/lib/api/ApiClientError";
import { apiPaths } from "@/lib/api/paths";

export class GameResource {
  constructor(private apiClient: ApiClient) {}

  public async get(id: number): Promise<unknown | ApiClientError> {
    return this.apiClient.get<unknown>(apiPaths.game.get(id));
  }

  public async update(id: number, data: Partial<unknown>): Promise<unknown | ApiClientError> {
    return this.apiClient.patch<unknown>(apiPaths.game.update(id), data);
  }
}

