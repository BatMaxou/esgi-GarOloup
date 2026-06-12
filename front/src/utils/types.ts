import {
  GameGlobalStepEnum,
  GameInitialisationStepEnum,
  GameRoleEnum,
  GameRuntimeStepEnum,
  GameTeamEnum,
  RoleEnum,
} from './enums';

// ------------------ Entity ------------------

export type User = {
  id: string;
  username?: string;
  email?: string;
  roles?: RoleEnum[];
  token?: string | null;
  refreshToken?: string | null;
};

export type TempUser = {
  id: string;
  username?: string;
  roles?: RoleEnum[];
};

export type Game = {
  id: string;
  globalStep?: GameGlobalStepEnum;
  initialisationStep?: GameInitialisationStepEnum;
  runtimeStep?: GameRuntimeStepEnum;
  joinCode?: string;
  players?: Player[];
  host?: Player;
  gameMaster?: Player;
  maxPlayers?: number;
  maxTimeForDiscussion?: number;
  public?: boolean;
  configuration?: Configuration;
  stepEndAt?: string;
  nights?: Night[];
  nightWorkflow?: Workflow;
  days?: Day[];
  dayWorkflow?: Workflow;
  votes?: Vote[];
};

export type Workflow = {
  id: string;
  steps?: GameRoleEnum[][];
  current?: number;
  currentTurn?: Record<GameRoleEnum, GameRoleEnum>;
  completed?: boolean;
};

export type GameRole = {
  role?: Role;
  type?: GameRoleEnum;
};

export type VillagerRole = GameRole & {
  friendId?: string;
};

export type SeerRole = GameRole & {
  lastObservedPlayerId?: string;
  lastObservedRole?: GameRoleEnum;
  observedRoles?: Record<string, GameRoleEnum>;
};

export type WerewolfRole = GameRole;

export type WitchRole = GameRole & {
  healPotionAvailable?: boolean;
  poisonPotionAvailable?: boolean;
};

export type Player = {
  id: string;
  user?: User;
  tempUser?: TempUser;
  dead?: boolean;
  game?: Game;
  host?: boolean;
  username?: string;
  role?: GameRole;
  team?: GameTeamEnum;
};

export type NightAction = {
  source?: GameRoleEnum | GameTeamEnum;
};

export type MurderAction = NightAction & {
  targetPlayerId?: string;
};

export type DayAction = {
  source?: GameRoleEnum | GameTeamEnum;
};

export type Night = {
  number?: number;
  resolved?: boolean;
  actions?: (NightAction | MurderAction)[];
};

export type Day = {
  number?: number;
  resolved?: boolean;
  actions?: DayAction[];
};

export type Vote = {
  number?: number;
  resolved?: boolean;
  ballots?: Ballot[];
};

export type Ballot = {
  player?: Player;
  target?: Player;
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

export type RolePlayable = Role & {
  type: GameRoleEnum;
  name: string;
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
  role?: GameRoleEnum;
  count?: number;
};

export type RoleDispatchEntry = {
  playerId: string;
  role: GameRoleEnum;
};

export type MercureToken = {
  token?: string;
};

// ------------------ Model ------------------

export type Homepage = {
  lastRoles: Role[];
  lastPublicGames: Game[];
};

export type WerewolfTeam = {
  gameId: string;
  members: Pick<Player, 'id' | 'username'>[];
};
