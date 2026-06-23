import { GameNightActionTypeEnum } from "./enums";
import { Game, MurderAction, Player, SaveAction } from "./types";

export const getLastUserKilledDuringNight = (game: Game) => {
  const currentNight = (game.nights?.length ?? 1) - 1;
  const lastNightMurderAction = game.nights?.[currentNight]?.actions?.find((action) => action.type === GameNightActionTypeEnum.MURDER) as MurderAction;
  return lastNightMurderAction?.targetPlayerId ?? null;
}

export const getLastUsernameUserKilledDuringNight = (game: Game, players: Player[]) => {
  const lastUserKilled = getLastUserKilledDuringNight(game);
  return lastUserKilled ? (players.find((player) => player.id === lastUserKilled)?.user?.username || players.find((player) => player.id === lastUserKilled)?.tempUser?.username) ?? null : null;
}

export const getHealUserDuringThisNight = (game: Game) => {
  const currentNight = (game.nights?.length ?? 1) - 1;
  const lastNightHealAction = game.nights?.[currentNight]?.actions?.find((action) => action.type === GameNightActionTypeEnum.SAVE) as SaveAction;
  return lastNightHealAction?.targetPlayerId ?? null;
}

export const getHealUsernameUserDuringThisNight = (game: Game, players: Player[]) => {
  const lastNightHealedUser = getHealUserDuringThisNight(game);
  return lastNightHealedUser ? (players.find((player) => player.id === lastNightHealedUser)?.user?.username || players.find((player) => player.id === lastNightHealedUser)?.tempUser?.username) ?? null : null;
}