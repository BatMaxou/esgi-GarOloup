import { GameStepEnum, RoleEnum } from "./enums";

export type User = {
  id: string;
  username?: string;
  email?: string;
  roles?: RoleEnum[];
}

export type TempUser = {
  id: string;
  username?: string;
  roles?: RoleEnum[];
}

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
};
