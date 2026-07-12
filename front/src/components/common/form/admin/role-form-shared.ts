import { GameRoleEnum, GameTeamEnum } from '@/utils/enums';
import type { Role } from '@/utils/types';

export type RolePayload = Partial<Omit<Role, 'type'>> & {
  type?: GameRoleEnum | null;
};

export type RoleFormValues = {
  name: string;
  description: string;
  ability: string;
  type: string;
  teams: GameTeamEnum[];
  minPlayers: string;
  maxPerGame: string;
  isPlayable: boolean;
  picture: File | null;
};

export const emptyRoleFormValues: RoleFormValues = {
  name: '',
  description: '',
  ability: '',
  type: '',
  teams: [],
  minPlayers: '',
  maxPerGame: '',
  isPlayable: true,
  picture: null,
};

export const mapRoleToFormValues = (role: Role): RoleFormValues => ({
  name: role.name ?? '',
  description: role.description ?? '',
  ability: role.ability ?? '',
  type: role.type ?? '',
  teams: role.teams ?? [],
  minPlayers: role.minPlayers != null ? String(role.minPlayers) : '',
  maxPerGame: role.maxPerGame != null ? String(role.maxPerGame) : '',
  isPlayable: Boolean(role.type),
  picture: null,
});

export const buildRolePayload = (formValues: RoleFormValues, mode: 'create' | 'update'): RolePayload => ({
  name: formValues.name.trim(),
  description: formValues.description.trim(),
  ability: formValues.ability.trim() || undefined,
  type: formValues.isPlayable ? (formValues.type as GameRoleEnum) : mode === 'update' ? null : undefined,
  teams: formValues.teams,
  minPlayers: parseInt(formValues.minPlayers, 10) || 0,
  maxPerGame: parseInt(formValues.maxPerGame, 10) || 0,
});
