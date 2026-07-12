import { ApiClient } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { apiPaths } from '@/lib/api/paths';
import type { RolePayload } from '@/components/common/form/admin/role-form-shared';
import type { Role } from '@/utils/types';

export class RoleResource {
  constructor(private apiClient: ApiClient) {}

  async getAll(): Promise<Role[] | ApiClientError> {
    return this.apiClient.get<Role[]>(apiPaths.role.list);
  }

  async get(id: string): Promise<Role | ApiClientError> {
    return this.apiClient.get<Role>(apiPaths.role.get(id));
  }

  async create(data: RolePayload): Promise<Role | ApiClientError> {
    return this.apiClient.post<Role>(apiPaths.role.create, data);
  }

  async update(id: string, data: RolePayload): Promise<Role | ApiClientError> {
    return this.apiClient.patch<Role>(apiPaths.role.update(id), data);
  }

  async updateFiles(id: string, data: FormData): Promise<Role | ApiClientError> {
    return this.apiClient.post<Role>(apiPaths.role.updateFiles(id), data);
  }
}
