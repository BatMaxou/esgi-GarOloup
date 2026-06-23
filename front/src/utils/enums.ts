export enum RoleEnum {
  USER = 'ROLE_USER',
  TEMP_USER = 'ROLE_TEMP_USER',
  ADMIN = 'ROLE_ADMIN',
}

export enum GameGlobalStepEnum {
  NEW = 'new',
  RUNNING = 'running',
  FINISH = 'finish',
}

export enum GameInitialisationStepEnum {
  NEW = 'new',
  CONFIGURATION = 'configuration',
  GAME_MASTER_CHOICE = 'game_master_choice',
  DISPATCH = 'dispatch',
  FINISH = 'finish',
}

export enum GameRuntimeStepEnum {
  SETUP = 'setup',
  NIGHT = 'night',
  DAY = 'day',
  VOTE = 'vote',
  FINISH = 'finish',
}

export enum ThemeEnum {
  LIGHT = 'light',
  DARK = 'dark',
}

export enum BugReportAreaEnum {
  HOME = 'home',
  AUTH = 'auth',
  ACCOUNT = 'account',
  LOBBY = 'lobby',
  GAME = 'game',
  ROLES = 'roles',
  OTHER = 'other',
}

export enum IdeaCategoryEnum {
  NEW_ROLE = 'new_role',
  GAME_MECHANIC = 'game_mechanic',
  UI_UX = 'ui_ux',
  GAME_MASTER = 'game_master',
  LOBBY_PARTY = 'lobby_party',
  SOCIAL_CHAT = 'social_chat',
  ACCESSIBILITY = 'accessibility',
  OTHER = 'other',
}

export enum GameNightActionTypeEnum {
  MURDER = 'murder',
  SAVE = 'save',
}

export enum GameRoleEnum {
  VILLAGER = 'villager',
  WEREWOLF = 'werewolf',
  SEER = 'seer',
  WITCH = 'witch',
  WILD_CHILD = 'wild_child',
}

export enum GameTeamEnum {
  VILLAGE = 'village',
  WEREWOLF = 'werewolf',
  SOLO = 'solo',
  COUPLE = 'couple',
}
