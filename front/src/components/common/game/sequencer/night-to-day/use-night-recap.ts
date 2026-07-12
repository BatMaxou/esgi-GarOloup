'use client';

import { useEffect, useState } from 'react';

import { GameRuntimeStepEnum } from '@/utils/enums';
import { Game, Player } from '@/utils/types';
import { buildRecapBeats, RecapBeat } from './recap-beats';

type UseNightRecapResult = {
  isPlaying: boolean;
  currentBeat: RecapBeat | null;
};

export const useNightRecap = (game: Game | null, player: Player | null): UseNightRecapResult => {
  const runtimeStep = game?.runtimeStep;
  const nightNumber = game?.nights?.length ?? 0;

  const [prevStep, setPrevStep] = useState<GameRuntimeStepEnum | undefined>(runtimeStep);
  const [playedNight, setPlayedNight] = useState<number | null>(null);
  const [beats, setBeats] = useState<RecapBeat[]>([]);
  const [beatIndex, setBeatIndex] = useState(0);

  if (runtimeStep !== prevStep) {
    setPrevStep(runtimeStep);

    if (
      game &&
      player &&
      prevStep === GameRuntimeStepEnum.NIGHT &&
      runtimeStep === GameRuntimeStepEnum.DAY &&
      playedNight !== nightNumber
    ) {
      setPlayedNight(nightNumber);
      setBeats(buildRecapBeats(game, game.players ?? []));
      setBeatIndex(0);
    }
  }

  useEffect(() => {
    if (beats.length === 0 || beatIndex >= beats.length) {
      return;
    }

    const timer = window.setTimeout(() => {
      setBeatIndex((current) => current + 1);
    }, beats[beatIndex].durationMs);

    return () => {
      window.clearTimeout(timer);
    };
  }, [beats, beatIndex]);

  const isPlaying = beats.length > 0 && beatIndex < beats.length;

  return {
    isPlaying,
    currentBeat: isPlaying ? beats[beatIndex] : null,
  };
};
