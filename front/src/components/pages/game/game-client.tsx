'use client';

import IngamePlayersSidebar from '@/components/common/game/ingame-players-sidebar';
import RunningGameDisplay from '@/components/common/game/running-game-display';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';
import { useMemo } from 'react';

const GameClient = () => {
  const { game } = useGame();
  const { player } = usePlayer();

  const playersList = useMemo(() => {
    if (!game) {
      return [];
    }

    const gameMasterUsername = game.gameMaster?.user?.username ?? game.gameMaster?.username;
    const normalizedGameMaster = game.gameMaster
      ? {
          id: game.gameMaster.id,
          user: gameMasterUsername
            ? {
                id: game.gameMaster.id,
                username: gameMasterUsername,
              }
            : undefined,
        }
      : null;

    return normalizedGameMaster ? [normalizedGameMaster, ...(game.players ?? [])] : [...(game.players ?? [])];
  }, [game]);

  if (!game || !player) {
    return <>Loading...</>;
  }

  const isHost = player?.id === game?.host?.id;

  return (
    <main className="flex h-full min-h-0 w-full flex-row justify-between items-start">
      <IngamePlayersSidebar players={playersList || []} />
      <RunningGameDisplay game={game} isHost={isHost} />
      <IngamePlayersSidebar players={playersList || []} />
    </main>
  );
};

export default GameClient;
