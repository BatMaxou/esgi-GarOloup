'use client';

import IngamePlayersSidebar from '@/components/common/game/ingame-players-sidebar';
import RunningGameDisplay from '@/components/common/game/running-game-display';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';
import type { Player } from '@/utils/types';
import { useMemo } from 'react';

const GameClient = () => {
  const { game } = useGame();
  const { player } = usePlayer();

  const playersList = useMemo(() => {
    if (!game) {
      return [];
    }

    const normalizedPlayers: Player[] = Array.isArray(game.players)
      ? game.players
      : (Object.values(game.players ?? {}) as Player[]);

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

    if (!normalizedGameMaster) {
      return normalizedPlayers;
    }

    const playersWithoutGameMaster = normalizedPlayers.filter(
      (currentPlayer) => currentPlayer.id !== normalizedGameMaster.id
    );
    return [normalizedGameMaster, ...playersWithoutGameMaster];
  }, [game]);

  if (!game || !player) {
    return <>Loading...</>;
  }

  const isHost = player?.id === game?.host?.id;
  const isGameMaster = player?.id === game?.gameMaster?.id;

  return (
    <main className="flex h-full min-h-0 w-full flex-row justify-between items-start">
      <IngamePlayersSidebar players={playersList || []} />
      <RunningGameDisplay isHost={isHost} isGameMaster={isGameMaster} />
      <IngamePlayersSidebar players={playersList || []} />
    </main>
  );
};

export default GameClient;
