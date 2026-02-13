'use client'

import { useCallback, useEffect, useState } from "react";

import Typography from "@/components/ui/atoms/typography";
import Button from "@/components/ui/molecules/button";
import { useApiClient } from "@/contexts/api-context";
import { useMercureClient } from "@/contexts/mercure-context";
import { Game } from "@/utils/types";
import { GameStepEnum } from "@/utils/enums";

type Props = {
  game: Game;
}

const TestClient = ({ game }: Props) => {
  const [currentStep, setCurrentStep] = useState<string>(game?.step ?? 'initialisation');
  const { apiClient } = useApiClient();
  const { mercureClient } = useMercureClient();

  useEffect(() => {
    console.log('-------- CLIENT SIDE ----------')
    console.log(game);
    console.log('------------------')
  }, []);

  useEffect(() => {
    const callable = (game: { step: GameStepEnum }) => {
      setCurrentStep(game.step);
    };

    mercureClient.watchGame('019c58aa-7230-7239-a8dc-62af8d19d9c9', callable);
  }, [mercureClient]);

  const changeStep = useCallback(async (step: GameStepEnum) => {
    await apiClient.game.update('019c58aa-7230-7239-a8dc-62af8d19d9c9', { step });
  }, [apiClient]);

  return <main className="p-8">
    <Typography tag="h1" variant="heading-1" bold center className="block">Page de test</Typography>

    <Button disabled={currentStep === 'initialisation'} label="Initialisation" onClick={() => changeStep(GameStepEnum.INITIALISATION)} />
    <Button disabled={currentStep === 'day'} label="Jour" onClick={() => changeStep(GameStepEnum.DAY)} />
    <Button disabled={currentStep === 'night'} label="Nuit" onClick={() => changeStep(GameStepEnum.NIGHT)} />
  </main>
}

export default TestClient;
