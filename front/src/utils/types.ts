import { GameRoleEnum, GameStepEnum, GameTeamEnum, RoleEnum } from './enums';

// ------------------ Entity ------------------

export type User = {
  id: string;
  username?: string;
  email?: string;
  roles?: RoleEnum[];
};

export type TempUser = {
  id: string;
  username?: string;
  roles?: RoleEnum[];
};

export type Game = {
  id: string;
  step?: GameStepEnum;
  joinCode?: string;
  players?: Player[];
  host?: Player;
};

export type Player = {
  id: string;
  user?: User;
  dead?: boolean;
  game?: Game;
  host?: boolean;
};

export type Role = {
  id: string;
  type?: GameRoleEnum;
  name?: string;
  description?: string;
  ability?: string;
  picture?: string;
  minPlayers?: number;
  maxPerGame?: number;
  teams?: GameTeamEnum[];
};

export type Configuration = {
  composition?: Composition;
  withGameMaster?: boolean;
  withRandomDispatch?: boolean;
};

export type Composition = {
  roles?: RoleEntry[];
};

export type RoleEntry = {
  role?: Role;
  count?: number;
};
