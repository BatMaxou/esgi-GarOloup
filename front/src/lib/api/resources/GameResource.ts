import { ApiClient } from "@/lib/api/ApiClient";
import { ApiClientError } from "../ApiClientError";
import { apiPaths } from "../paths";

export class GameResource {
  constructor(private apiClient: ApiClient) {}

  public async update(id: number, data: Partial<unknown>): Promise<unknown | ApiClientError> {
    return this.apiClient.patch<unknown>(apiPaths.game.update(id), data);
  }
}

