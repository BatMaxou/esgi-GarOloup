'use client';

import Typography from '@/components/ui/atoms/typography';
import { useGame } from '@/contexts/game-context';
import { useEffect } from 'react';

const GameClient = () => {
  const { game } = useGame();

  useEffect(() => {
    console.log('game', game);
  }, [game]);

  if (!game) {
    return <>Loading...</>;
  }

  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-1" bold center className="block">
        Page de test de la partie {game.id ?? 'non trouvée'}
      </Typography>

      <Typography>Step: {game.step}</Typography>
    </main>
  );
};

export default GameClient;
