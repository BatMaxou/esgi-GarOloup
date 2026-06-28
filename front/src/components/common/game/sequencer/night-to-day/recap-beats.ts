import { GameRoleEnum } from '@/utils/enums';
import { getDeadUsersDuringNight, getRevealUserDuringThisNight } from '@/utils/game';
import { Game, Player } from '@/utils/types';

export type RecapBeatType = 'death' | 'reveal' | 'revealDead' | 'calm';

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
  revealDead: 4000,
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

export const buildPrivateBeats = (game: Game, players: Player[], player: Player): RecapBeat[] => {
  const reveal = getRevealUserDuringThisNight(game, player);
  if (!reveal) {
    return [];
  }

  const observedDiedThisNight = getDeadUsersDuringNight(game).includes(reveal.observedPlayerId);
  const type: RecapBeatType = observedDiedThisNight ? 'revealDead' : 'reveal';

  return [
    {
      id: `reveal-${reveal.observedPlayerId}`,
      type,
      durationMs: BEAT_DURATIONS[type],
      username: resolveUsername(players, reveal.observedPlayerId),
      role: reveal.observedRole,
    },
  ];
};

export const buildRecapBeats = (game: Game, players: Player[], player: Player): RecapBeat[] => {
  const publicBeats = buildPublicBeats(game, players);
  const privateBeats = buildPrivateBeats(game, players, player);

  const openingBeats: RecapBeat[] =
    publicBeats.length === 0 ? [{ id: 'calm', type: 'calm', durationMs: BEAT_DURATIONS.calm }] : publicBeats;

  return [...openingBeats, ...privateBeats];
};
