import { GameRoleEnum } from '@/utils/enums';
import { getRevealUserDuringThisNight } from '@/utils/game';
import { Game, Player } from '@/utils/types';

export type RecapBeatType = 'reveal';

export type RecapBeat = {
  id: string;
  type: RecapBeatType;
  durationMs: number;
  username?: string | null;
  role?: GameRoleEnum | null;
};

export const BEAT_DURATIONS: Record<RecapBeatType, number> = {
  reveal: 4000,
};

const resolveUsername = (players: Player[], playerId: string): string | null => {
  const target = players.find((player) => player.id === playerId);
  return target?.user?.username ?? target?.tempUser?.username ?? null;
};

export const buildSeerRevealBeat = (
  players: Player[],
  observedPlayerId: string,
  observedRole: GameRoleEnum
): RecapBeat[] => {
  const type: RecapBeatType = 'reveal';

  return [
    {
      id: `reveal-${observedPlayerId}`,
      type,
      durationMs: BEAT_DURATIONS[type],
      username: resolveUsername(players, observedPlayerId),
      role: observedRole,
    },
  ];
};

export const buildSeerRevealBeats = (game: Game, players: Player[], player: Player): RecapBeat[] => {
  const reveal = getRevealUserDuringThisNight(game, player);
  if (!reveal) {
    return [];
  }

  return buildSeerRevealBeat(players, reveal.observedPlayerId, reveal.observedRole);
};

export const buildRecapBeats = (game: Game, players: Player[], player: Player): RecapBeat[] => {
  const seerRevealBeat = buildSeerRevealBeats(game, players, player);

  return [...seerRevealBeat];
};
