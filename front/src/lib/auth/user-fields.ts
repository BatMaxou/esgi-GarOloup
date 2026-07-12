import { RoleEnum } from '@/utils/enums';
import type { User } from '@/utils/types';

export const userAdditionalFields = {
  token: {
    type: 'string',
    input: true,
    returned: true,
    required: false,
  },
  refreshToken: {
    type: 'string',
    input: true,
    returned: true,
    required: false,
  },
  username: {
    type: 'string',
    returned: true,
    required: false,
  },
  roles: {
    type: 'json',
    returned: true,
    required: false,
  },
} as const;

export const normalizeRoles = (roles: unknown): RoleEnum[] => {
  if (Array.isArray(roles)) {
    return roles as RoleEnum[];
  }

  if (typeof roles === 'string') {
    try {
      const parsed = JSON.parse(roles);
      return Array.isArray(parsed) ? (parsed as RoleEnum[]) : [];
    } catch {
      return [];
    }
  }

  return [];
};

export const mapApiUserToAuthUser = (user: User, email: string) => ({
  email: user.email ?? email,
  name: user.username ?? user.email ?? email,
  username: user.username,
  roles: user.roles ?? [],
});

export const hasAdminRole = (roles: unknown): boolean => normalizeRoles(roles).includes(RoleEnum.ADMIN);
