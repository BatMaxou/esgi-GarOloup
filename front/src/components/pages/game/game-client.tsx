'use client';

import IngamePlayersSidebar from '@/components/common/game/ingame-players-sidebar';
import RunningGameDisplay from '@/components/common/game/running-game-display';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';

const GameClient = () => {
  const { game } = useGame();
  const { player } = usePlayer();

  if (!game || !player) {
    return <>Loading...</>;
  }

  const isHost = player?.id === game?.host?.id;

  return (
    <main className="flex h-full min-h-0 w-full flex-row justify-between items-start">
      <IngamePlayersSidebar players={game.players || []} />
      <RunningGameDisplay game={game} isHost={isHost} />
      <IngamePlayersSidebar players={game.players || []} />
    </main>
  );
};

export default GameClient;
