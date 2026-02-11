'use client'

import { useCallback, useEffect, useState } from "react";

import Typography from "@/components/ui/atoms/typography";
import Button from "@/components/ui/molecules/button";
import { MercureClient } from "@/lib/mercure/MercureClient";
import { apiBaseUrl, mercureUrl } from "@/utils/tools";
import { ApiClient } from "@/lib/api/ApiClient";

const TestPage = () => {
  const [currentStep, setCurrentStep] = useState<string>('initialisation');

  console.log('------------------')
  console.log(mercureUrl);

  useEffect(() => {
    const callable = (game: { step: string }) => {
      setCurrentStep(game.step);
    };

    const client = new MercureClient(mercureUrl, apiBaseUrl);
    client.watchGame(3, callable);
  }, []);

  const changeStep = useCallback(async (step: string) => {
    const client = new ApiClient(apiBaseUrl);
    await client.game.update(3, { step });
  }, []);

  return <main className="p-8">
    <Typography tag="h1" variant="heading-1" bold center className="block">Page de test</Typography>

    <Button disabled={currentStep === 'initialisation'} label="Initialisation" onClick={() => changeStep('initialisation')} />
    <Button disabled={currentStep === 'day'} label="Jour" onClick={() => changeStep('day')} />
    <Button disabled={currentStep === 'night'} label="Nuit" onClick={() => changeStep('night')} />
  </main>
}

export default TestPage;
