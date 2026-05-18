'use client';

import IngamePlayersSidebar from '@/components/common/game/ingame-players-sidebar';
import RunningGame from '@/components/common/game/running-game';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';

const GameClient = () => {
  const { game } = useGame();
  const { player } = usePlayer();

  if (!game || !player) {
    return <>Loading...</>;
  }

  return (
    <main className="flex h-full min-h-0 w-full flex-row justify-between items-start">
      <IngamePlayersSidebar />
      <RunningGame />
      <IngamePlayersSidebar />
    </main>
  );
};

export default GameClient;
