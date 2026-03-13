import { ReactNode } from 'react';
import { notFound } from 'next/navigation';

import { PlayerProvider } from '@/contexts/player-context';
import { GameProvider } from '@/contexts/game-context';
import { getApiClient } from '@/utils/server/clients';
import { ApiClientError } from '@/lib/api/ApiClientError';

type Props = {
  children: ReactNode;
};

const GameLayout = async ({ children }: Props) => {
  const apiClient = await getApiClient();

  if (!apiClient.token) {
    return notFound();
  }

  const maybeGame = await apiClient.game.getCurrent();
  if (maybeGame instanceof ApiClientError) {
    return notFound();
  }

  const maybePlayer = await apiClient.player.getCurrent();
  if (maybePlayer instanceof ApiClientError) {
    return notFound();
  }

  return (
    <PlayerProvider initialPlayer={maybePlayer}>
      <GameProvider initialGame={maybeGame}>{children}</GameProvider>
    </PlayerProvider>
  );
};

export default GameLayout;
