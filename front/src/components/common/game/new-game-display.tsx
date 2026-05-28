'use client';

import { useCallback } from 'react';
import Typography from '@/components/ui/atoms/typography';
import Icon from '@/components/ui/atoms/icon';
import { GameInitialisationStepEnum } from '@/utils/enums';
import { Game } from '@/utils/types';
import { useTranslations } from 'next-intl';
import Button from '@/components/ui/molecules/button';
import { useGame } from '@/contexts/game-context';
import GameConfigForm from '../form/game/game-config-form';
import Card from '@/components/ui/molecules/card';
import { ArrowLeftIcon } from 'lucide-react';

const NewGameDisplay = ({ game, isHost }: { game: Game; isHost: boolean }) => {
  const { openInvitation, closeInvitation } = useGame();

  const initialisationStep = game?.initialisationStep;
  // const gameConfiguration = game?.configuration ?? {};
  const t = useTranslations('components.common.game.new-game-display');

  const renderInitialisationStep = useCallback(() => {
    switch (initialisationStep) {
      case GameInitialisationStepEnum.NEW:
        return (
          <>
            <Typography variant="subtitle" className="animate-pulse">
              En attente de joueurs
            </Typography>
            <Typography variant="body" className="animate-pulse">
              {game?.players?.length ?? 0} / {game?.maxPlayers ?? 0} {t('players')}
            </Typography>
          </>
        );
      case GameInitialisationStepEnum.CONFIGURATION:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            L&apos;hôte configure la partie
          </Typography>
        );
      case GameInitialisationStepEnum.GAME_MASTER_CHOICE:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            L&apos;hôte choisit le maître de jeu
          </Typography>
        );
      case GameInitialisationStepEnum.DISPATCH:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            Le maître de jeu distribue les rôles
          </Typography>
        );
      case GameInitialisationStepEnum.FINISH:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            Lancement de la partie
          </Typography>
        );
      default:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            Chargement en cours...
          </Typography>
        );
    }
  }, [initialisationStep, game, t]);

  const renderHostActions = useCallback(() => {
    switch (initialisationStep) {
      case GameInitialisationStepEnum.NEW:
        return (
          <>
            {game.players?.length !== game.maxPlayers ? (
              <Typography variant="body">Vous pouvez commencer !</Typography>
            ) : null}
            <Button
              variant="gradient"
              label="Configurer la partie"
              // disabled={game.players?.length !== game.maxPlayers}
              onClick={closeInvitation}
            />
          </>
        );
      case GameInitialisationStepEnum.CONFIGURATION:
        return (
          <>
            <div className="flex flex-row items-center justify-between gap-2">
              <ArrowLeftIcon className="w-4 h-4 mb-1 text-neutral-400" />
              <Button
                variant="text"
                className="text-neutral-400 hover:text-neutral-200"
                label={t('openInvitation')}
                onClick={openInvitation}
              />
            </div>
            <Card className="w-full px-20! py-10!" orientation="vertical" hoverable={false}>
              <GameConfigForm />
            </Card>
          </>
        );
      case GameInitialisationStepEnum.GAME_MASTER_CHOICE:
        return null;
      case GameInitialisationStepEnum.DISPATCH:
        return null;
      case GameInitialisationStepEnum.FINISH:
        return null;
      default:
        return null;
    }
  }, [initialisationStep, openInvitation, closeInvitation, game, t]);

  return (
    <div className="flex flex-col items-center justify-center gap-8 h-full w-full bg-background/80">
      {(isHost && initialisationStep === GameInitialisationStepEnum.NEW) || !isHost ? (
        <Icon name="garoloup" className="animate-pulse w-20 h-20" />
      ) : null}
      <div className="flex flex-col items-center justify-center gap-2">
        {(isHost && initialisationStep === GameInitialisationStepEnum.NEW) || !isHost
          ? renderInitialisationStep()
          : null}
      </div>
      <div className="flex flex-col items-center justify-center gap-2">{isHost && renderHostActions()}</div>
    </div>
  );
};

export default NewGameDisplay;
