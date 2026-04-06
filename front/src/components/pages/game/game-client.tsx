'use client';

import { useCallback } from 'react';

import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import { useApiClient } from '@/contexts/api-context';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';
import { GameStepEnum } from '@/utils/enums';

const GameClient = () => {
  const { game } = useGame();
  const { player } = usePlayer();
  const { apiClient } = useApiClient();

  const closeInvitation = useCallback(() => {
    apiClient.game.close();
  }, [apiClient]);

  const openInvitation = useCallback(() => {
    apiClient.game.open();
  }, [apiClient]);

  if (!game || !player) {
    return <>Loading...</>;
  }

  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-1" bold center className="block">
        Page de test de la partie {game.id ?? 'non trouvée'}
      </Typography>

      <Typography tag="p">Step: {game.step}</Typography>
      <Typography tag="p">Players: {game.players?.length}</Typography>

      {player.host && (
        <div>
          {game.step === GameStepEnum.NEW && <Button onClick={closeInvitation} label="Close invitation" />}
          {game.step === GameStepEnum.CONFIGURATION && <Button onClick={openInvitation} label="Open invitation" />}
        </div>
      )}
    </main>
  );
};

export default GameClient;
