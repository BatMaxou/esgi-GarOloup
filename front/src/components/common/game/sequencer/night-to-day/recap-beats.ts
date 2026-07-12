import { GameRoleEnum } from '@/utils/enums';
import { getDeadUsersDuringNight } from '@/utils/game';
import { Game, Player } from '@/utils/types';

export type RecapBeatType = 'death' | 'calm';

export type RecapBeat = {
  id: string;
  type: RecapBeatType;
  durationMs: number;
  username?: string | null;
  role?: GameRoleEnum | null;
};

export const BEAT_DURATIONS: Record<RecapBeatType, number> = {
  death: 3500,
  calm: 3500,
};

const resolveUsername = (players: Player[], playerId: string): string | null => {
  const target = players.find((player) => player.id === playerId);
  return target?.user?.username ?? target?.tempUser?.username ?? null;
};

export const buildPublicBeats = (game: Game, players: Player[]): RecapBeat[] =>
  getDeadUsersDuringNight(game).map((playerId) => ({
    id: `death-${playerId}`,
    type: 'death',
    durationMs: BEAT_DURATIONS.death,
    username: resolveUsername(players, playerId),
  }));

export const buildRecapBeats = (game: Game, players: Player[], player: Player): RecapBeat[] => {
  const publicBeats = buildPublicBeats(game, players);
  // const privateBeats = buildPrivateBeats(game, players, player);

  const openingBeats: RecapBeat[] =
    publicBeats.length === 0 ? [{ id: 'calm', type: 'calm', durationMs: BEAT_DURATIONS.calm }] : publicBeats;

  return [...openingBeats];
};
