import { ReactNode } from 'react';
import { notFound } from 'next/navigation';

import { PlayerProvider } from '@/contexts/player-context';
import { GameProvider } from '@/contexts/game-context';
import { RoleProvider } from '@/contexts/role-context';
import { getApiClient, getSession } from '@/utils/server/clients';
import { ApiClientError } from '@/lib/api/ApiClientError';
import IngameNavbar from '@/components/common/layout/ingame-navbar';

type Props = {
  children: ReactNode;
};

const GameLayout = async ({ children }: Props) => {
  const session = await getSession();

  if (!session?.user?.token) {
    return notFound();
  }

  const apiClient = await getApiClient();

  const maybeGame = await apiClient.game.getCurrent();
  if (maybeGame instanceof ApiClientError) {
    return notFound();
  }

  const maybePlayer = await apiClient.player.getCurrent();
  if (maybePlayer instanceof ApiClientError) {
    return notFound();
  }

  return (
    <RoleProvider>
      <PlayerProvider initialPlayer={maybePlayer}>
        <GameProvider initialGame={maybeGame}>
          <div className="grid min-h-dvh grid-rows-[auto_1fr]">
            <IngameNavbar />
            <div className="min-h-0">{children}</div>
          </div>
        </GameProvider>
      </PlayerProvider>
    </RoleProvider>
  );
};

export default GameLayout;
