import { GameNightActionTypeEnum, GameRoleEnum } from './enums';
import { Game, MurderAction, NightCurrentTurn, Player, SaveAction, SeerRole } from './types';

export const getActiveNightTurnRoles = (currentTurn?: NightCurrentTurn): GameRoleEnum[] => {
  if (!currentTurn) {
    return [];
  }

  if (Array.isArray(currentTurn)) {
    return currentTurn;
  }

  return Object.values(currentTurn);
};

export const isSeerNightTurn = (currentTurn?: NightCurrentTurn): boolean =>
  getActiveNightTurnRoles(currentTurn).includes(GameRoleEnum.SEER);

export const isRoleNightTurn = (currentTurn: NightCurrentTurn | undefined, role: GameRoleEnum): boolean =>
  getActiveNightTurnRoles(currentTurn).includes(role);

export const getCurrentNightNumber = (game: Game): number => Math.max(0, (game.nights?.length ?? 1) - 1);

export const getLatestSeerReveal = (
  player: Player
): { observedPlayerId: string; observedRole: GameRoleEnum } | null => {
  const role = player.role;
  if (!role || role.type !== GameRoleEnum.SEER) {
    return null;
  }

  const seerRole = role as SeerRole;
  const observedPlayerId = seerRole.lastObservedPlayerId;
  if (!observedPlayerId) {
    return null;
  }

  const observedRole = seerRole.observedRoles?.[observedPlayerId];
  if (!observedRole) {
    return null;
  }

  return { observedPlayerId, observedRole };
};

/////////////////////////////////////////////////////////////////////
//////////////////////// Public Informations ////////////////////////
/////////////////////////////////////////////////////////////////////

export const getLastUserKilledDuringNight = (game: Game) => {
  const currentNight = (game.nights?.length ?? 1) - 1;
  const lastNightMurderAction = game.nights?.[currentNight]?.actions?.find(
    (action) => action.type === GameNightActionTypeEnum.MURDER
  ) as MurderAction;
  return lastNightMurderAction?.targetPlayerId ?? null;
};

export const getLastUsernameUserKilledDuringNight = (game: Game, players: Player[]) => {
  const lastUserKilled = getLastUserKilledDuringNight(game);
  return lastUserKilled
    ? ((players.find((player) => player.id === lastUserKilled)?.user?.username ||
        players.find((player) => player.id === lastUserKilled)?.tempUser?.username) ??
        null)
    : null;
};

export const getAllUsersKilledDuringNight = (game: Game): string[] => {
  const currentNight = (game.nights?.length ?? 1) - 1;
  return (game.nights?.[currentNight]?.actions ?? [])
    .filter((action) => action.type === GameNightActionTypeEnum.MURDER)
    .map((action) => (action as MurderAction).targetPlayerId)
    .filter((targetPlayerId): targetPlayerId is string => Boolean(targetPlayerId));
};

// Action Heal de la witch
// À trasférer dans la section private informations quand mis en place en back

export const getHealUserDuringThisNight = (game: Game) => {
  const currentNight = (game.nights?.length ?? 1) - 1;
  const lastNightHealAction = game.nights?.[currentNight]?.actions?.find(
    (action) => action.type === GameNightActionTypeEnum.SAVE
  ) as SaveAction;
  return lastNightHealAction?.targetPlayerId ?? null;
};

export const getHealUsernameUserDuringThisNight = (game: Game, players: Player[]) => {
  const lastNightHealedUser = getHealUserDuringThisNight(game);
  return lastNightHealedUser
    ? ((players.find((player) => player.id === lastNightHealedUser)?.user?.username ||
        players.find((player) => player.id === lastNightHealedUser)?.tempUser?.username) ??
        null)
    : null;
};

export const getDeadUsersDuringNight = (game: Game): string[] => {
  const killed = getAllUsersKilledDuringNight(game);
  const realDeadPlayers = game.players?.filter((player) => player.dead);
  return killed.filter((playerId) => realDeadPlayers?.some((player) => player.id === playerId));
};

/////////////////////////////////////////////////////////////////////
//////////////////////// Private Informations ///////////////////////
/////////////////////////////////////////////////////////////////////

export const getRevealUserDuringThisNight = (
  game: Game,
  player: Player
): { observedPlayerId: string; observedRole: GameRoleEnum } | null => getLatestSeerReveal(player);

export const getRevealUsernameUserDuringThisNight = (game: Game, players: Player[], player: Player) => {
  const reveal = getRevealUserDuringThisNight(game, player);
  if (!reveal) {
    return null;
  }

  const observedPlayer = players.find((candidate) => candidate.id === reveal.observedPlayerId);
  return observedPlayer?.user?.username ?? observedPlayer?.tempUser?.username ?? null;
};
