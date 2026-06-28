import { GameRoleEnum } from '@/utils/enums';
import {
  getAllUsersKilledDuringNight,
  getRevealUserDuringThisNight,
} from '@/utils/game';
import { Game, Player } from '@/utils/types';

export type RecapBeatType = 'death' | 'reveal' | 'calm';

export type RecapBeat = {
  id: string;
  type: RecapBeatType;
  durationMs: number;
  username?: string | null;
  role?: GameRoleEnum | null;
};

export const BEAT_DURATIONS: Record<RecapBeatType, number> = {
  death: 3500,
  reveal: 4000,
  calm: 2500,
};

const resolveUsername = (players: Player[], playerId: string): string | null => {
  const target = players.find((player) => player.id === playerId);
  return target?.user?.username ?? target?.tempUser?.username ?? null;
};

export const buildPublicBeats = (game: Game, players: Player[]): RecapBeat[] =>
  getAllUsersKilledDuringNight(game).map((playerId) => ({
    id: `death-${playerId}`,
    type: 'death',
    durationMs: BEAT_DURATIONS.death,
    username: resolveUsername(players, playerId),
  }));

export const buildPrivateBeats = (game: Game, players: Player[], player: Player): RecapBeat[] => {
  const reveal = getRevealUserDuringThisNight(game, player);
  if (!reveal) {
    return [];
  }

  return [
    {
      id: `reveal-${reveal.observedPlayerId}`,
      type: 'reveal',
      durationMs: BEAT_DURATIONS.reveal,
      username: resolveUsername(players, reveal.observedPlayerId),
      role: reveal.observedRole,
    },
  ];
};

export const buildRecapBeats = (game: Game, players: Player[], player: Player): RecapBeat[] => {
  const beats = [...buildPublicBeats(game, players), ...buildPrivateBeats(game, players, player)];

  if (beats.length === 0) {
    return [{ id: 'calm', type: 'calm', durationMs: BEAT_DURATIONS.calm }];
  }

  return beats;
};
