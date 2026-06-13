'use client';

import IngamePlayersSidebar from '@/components/common/game/ingame-players-sidebar';
import RunningGameDisplay from '@/components/common/game/running-game-display';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';
import { SeerProvider } from '@/contexts/roles/seer-context';
import { WereWolfProvider } from '@/contexts/roles/werewolf-context';
import { WitchProvider } from '@/contexts/roles/witch-context';
import { GameRoleEnum } from '@/utils/enums';
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
  const isHost = player?.id === game?.host?.id;
  const isGameMaster = player?.id === game?.gameMaster?.id;

  const renderGameDisplay = useMemo(() => {
    switch (true) {
      case player?.role?.type === GameRoleEnum.WEREWOLF:
        return (
          <WereWolfProvider>
            <RunningGameDisplay isHost={isHost} isGameMaster={isGameMaster} />
          </WereWolfProvider>
        );
      case player?.role?.type === GameRoleEnum.WITCH:
        return (
          <WitchProvider>
            <RunningGameDisplay isHost={isHost} isGameMaster={isGameMaster} />
          </WitchProvider>
        );
      case player?.role?.type === GameRoleEnum.SEER:
        return (
          <SeerProvider>
            <RunningGameDisplay isHost={isHost} isGameMaster={isGameMaster} />
          </SeerProvider>
        );
      default:
        return <RunningGameDisplay isHost={isHost} isGameMaster={isGameMaster} />;
    }
  }, [isGameMaster, isHost, player?.role?.type]);

  if (!game || !player) {
    return <>Loading...</>;
  }

  return (
    <main className="flex h-full min-h-0 w-full flex-row justify-between items-start">
      <IngamePlayersSidebar players={playersList || []} />
      <div className="px-8 py-8">{renderGameDisplay}</div>
      <IngamePlayersSidebar players={playersList || []} />
    </main>
  );
};

export default GameClient;
