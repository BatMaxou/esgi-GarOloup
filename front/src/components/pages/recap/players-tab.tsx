import type { Recap } from '@/utils/types';
import PlayerCard from './player-card';

type Props = {
  recap: Recap;
};

const PlayersTab = ({ recap }: Props) => {
  const winners = recap.players.filter((p) => p.team === recap.winningTeam);
  const others = recap.players.filter((p) => p.team !== recap.winningTeam);

  return (
    <div className="flex flex-col gap-8 pt-6 w-full">
      <div className="flex flex-wrap justify-center gap-3">
        {winners.map((player) => (
          <PlayerCard key={player.playerId} player={player} />
        ))}
      </div>

      {others.length > 0 && (
        <>
          <div className="h-0.5 w-full rounded-full bg-primary/20" />
          <div className="flex flex-wrap justify-center gap-3">
            {others.map((player) => (
              <PlayerCard key={player.playerId} player={player} />
            ))}
          </div>
        </>
      )}
    </div>
  );
};

export default PlayersTab;
