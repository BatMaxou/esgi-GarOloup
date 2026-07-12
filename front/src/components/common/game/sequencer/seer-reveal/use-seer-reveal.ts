'use client';

import { useEffect, useState } from 'react';

import { GameRoleEnum, GameRuntimeStepEnum } from '@/utils/enums';
import { getCurrentNightNumber, getLatestSeerReveal, isSeerNightTurn } from '@/utils/game';
import { Game, Player } from '@/utils/types';
import { buildSeerRevealBeat, RecapBeat } from './recap-beats';

type UseSeerRevealResult = {
  isPlaying: boolean;
  currentBeat: RecapBeat | null;
};

export const useSeerReveal = (game: Game | null, player: Player | null): UseSeerRevealResult => {
  const runtimeStep = game?.runtimeStep;
  const isSeerTurn = isSeerNightTurn(game?.nightWorkflow?.currentTurn);
  const nightNumber = game ? getCurrentNightNumber(game) : 0;

  const [prevIsSeerTurn, setPrevIsSeerTurn] = useState(isSeerTurn);
  const [pendingNight, setPendingNight] = useState<number | null>(null);
  const [playedNight, setPlayedNight] = useState<number | null>(null);
  const [beats, setBeats] = useState<RecapBeat[]>([]);
  const [beatIndex, setBeatIndex] = useState(0);

  if (isSeerTurn !== prevIsSeerTurn) {
    setPrevIsSeerTurn(isSeerTurn);

    if (prevIsSeerTurn && !isSeerTurn && game && player && player.role?.type === GameRoleEnum.SEER) {
      setPendingNight(nightNumber);
    }
  }

  if (
    game &&
    player &&
    player.role?.type === GameRoleEnum.SEER &&
    runtimeStep === GameRuntimeStepEnum.NIGHT &&
    pendingNight === nightNumber &&
    playedNight !== nightNumber &&
    !isSeerTurn
  ) {
    const reveal = getLatestSeerReveal(player);

    if (reveal) {
      setPendingNight(null);
      setPlayedNight(nightNumber);
      setBeats(buildSeerRevealBeat(game.players ?? [], reveal.observedPlayerId, reveal.observedRole));
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
