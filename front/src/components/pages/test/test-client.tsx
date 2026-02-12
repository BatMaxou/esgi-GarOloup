'use client'

import { useCallback, useEffect, useState } from "react";

import Typography from "@/components/ui/atoms/typography";
import Button from "@/components/ui/molecules/button";
import { useApiClient } from "@/contexts/api-context";
import { useMercureClient } from "@/contexts/mercure-context";

type Props = {
  game: unknown;
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
    const callable = (game: { step: string }) => {
      setCurrentStep(game.step);
    };

    mercureClient.watchGame(3, callable);
  }, [mercureClient]);

  const changeStep = useCallback(async (step: string) => {
    await apiClient.game.update(3, { step });
  }, [apiClient]);

  return <main className="p-8">
    <Typography tag="h1" variant="heading-1" bold center className="block">Page de test</Typography>

    <Button disabled={currentStep === 'initialisation'} label="Initialisation" onClick={() => changeStep('initialisation')} />
    <Button disabled={currentStep === 'day'} label="Jour" onClick={() => changeStep('day')} />
    <Button disabled={currentStep === 'night'} label="Nuit" onClick={() => changeStep('night')} />
  </main>
}

export default TestClient;
