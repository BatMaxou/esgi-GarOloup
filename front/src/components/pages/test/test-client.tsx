'use client';

import { useEffect, useState } from 'react';

import Typography from '@/components/ui/atoms/typography';
import { useMercureClient } from '@/contexts/mercure-context';
import { Game } from '@/utils/types';
import { GameStepEnum } from '@/utils/enums';

type Props = {
  game: Game;
};

const TestClient = ({ game }: Props) => {
  const [currentStep, setCurrentStep] = useState<string>(
    game?.step ?? GameStepEnum.NEW
  );
  const { mercureClient } = useMercureClient();

  useEffect(() => {
    console.log('-------- CLIENT SIDE ----------');
    console.log(game);
    console.log('------------------');
  }, [game]);

  useEffect(() => {
    const callable = (game: Game) => game.step && setCurrentStep(game.step);
    mercureClient.watchGame(game.id, callable);
  }, [mercureClient, game]);

  // const changeStep = useCallback(async (step: GameStepEnum) => {
  //   await apiClient.game.update(game.id, { step });
  // }, [apiClient, game]);

  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-1" bold center className="block">
        Page de test
      </Typography>
      {currentStep}

      {/*
      <Button disabled={currentStep === GameStepEnum.NEW} label="Initialisation" onClick={() => changeStep(GameStepEnum.NEW)} />
      <Button disabled={currentStep === GameStepEnum.DAY} label="Jour" onClick={() => changeStep(GameStepEnum.DAY)} />
      <Button disabled={currentStep === GameStepEnum.NIGHT} label="Nuit" onClick={() => changeStep(GameStepEnum.NIGHT)} />
    */}
    </main>
  );
};

export default TestClient;
