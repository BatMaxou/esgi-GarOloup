'use client';

import { useCallback, useState } from 'react';
import { useTranslations } from 'next-intl';
import { ArrowLeftIcon } from 'lucide-react';
import Button from '@/components/ui/molecules/button';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import { useGame } from '@/contexts/game-context';
import { GameInitialisationStepEnum } from '@/utils/enums';
import { Game, RoleEntry, RolePlayable } from '@/utils/types';
import TooMuchPlayersDialog from './configuration/too-much-players-dialog';
import GameConfigForm from '../form/game/game-config-form';
import { minPlayersToLaunchGame } from '@/utils/tools';
import GameMasterChoiceForm from '../form/game/game-master-choice-form';

const NewGameDisplay = ({ game, isHost }: { game: Game; isHost: boolean }) => {
  const { openInvitation, closeInvitation, resetConfiguration } = useGame();
  const [openTooMuchPlayersDialog, setOpenTooMuchPlayersDialog] = useState(false);
  const [tooMuchPlayersDialogData, setTooMuchPlayersDialogData] = useState<{
    currentRoles: RoleEntry[];
    playableRoleList: RolePlayable[];
    onCompositionChange: (roles: RoleEntry[]) => void;
  } | null>(null);
  const initialisationStep = game?.initialisationStep;
  // const gameConfiguration = game?.configuration ?? {};
  const t = useTranslations('components.common.game.new-game-display');

  const renderInitialisationStep = useCallback(() => {
    switch (initialisationStep) {
      case GameInitialisationStepEnum.NEW:
        return (
          <>
            <Typography variant="subtitle" className="animate-pulse">
              {t('waitingForPlayers')}
            </Typography>
            <Typography variant="body" className="animate-pulse">
              {game?.players?.length ?? 0} / {game?.maxPlayers ?? 0} {t('players')}
            </Typography>
          </>
        );
      case GameInitialisationStepEnum.CONFIGURATION:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            {t('hostConfiguring')}
          </Typography>
        );
      case GameInitialisationStepEnum.GAME_MASTER_CHOICE:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            {t('hostChoosingGameMaster')}
          </Typography>
        );
      case GameInitialisationStepEnum.DISPATCH:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            {t('gameMasterDispatching')}
          </Typography>
        );
      case GameInitialisationStepEnum.FINISH:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            {t('launchingGame')}
          </Typography>
        );
      default:
        return (
          <Typography variant="subtitle" className="animate-pulse">
            {t('loading')}
          </Typography>
        );
    }
  }, [initialisationStep, game, t]);

  const renderHostActions = useCallback(() => {
    switch (initialisationStep) {
      case GameInitialisationStepEnum.NEW:
        return (
          <>
            {(game.players?.length || 0) >= minPlayersToLaunchGame ? (
              <Typography variant="body">{t('canStart')}</Typography>
            ) : null}
            <Button
              variant="accent"
              label={t('configureGame')}
              disabled={(game.players?.length || 0) < minPlayersToLaunchGame}
              onClick={closeInvitation}
            />
          </>
        );
      case GameInitialisationStepEnum.CONFIGURATION:
        return (
          <>
            <div className="flex flex-row items-center justify-start w-full gap-2">
              <ArrowLeftIcon className="w-4 h-4 mb-1 text-neutral-400" />
              <Button
                variant="text"
                className="text-neutral-400 hover:text-neutral-200"
                label={t('openInvitation')}
                onClick={openInvitation}
              />
            </div>
            <Card className="w-full px-20! py-10!" orientation="vertical" hoverable={false}>
              <GameConfigForm
                onTooMuchPlayers={(payload) => {
                  setTooMuchPlayersDialogData(payload);
                  setOpenTooMuchPlayersDialog(true);
                }}
              />
            </Card>
          </>
        );
      case GameInitialisationStepEnum.GAME_MASTER_CHOICE:
        return (
          <>
            <div className="flex flex-row items-center justify-start w-full gap-2">
              <ArrowLeftIcon className="w-4 h-4 mb-1 text-neutral-400" />
              <Button
                variant="text"
                className="text-neutral-400 hover:text-neutral-200"
                label={t('goBackToConfiguration')}
                onClick={() => resetConfiguration()}
              />
            </div>
            <Card className="w-full px-20! py-10!" orientation="vertical" hoverable={false}>
              <GameMasterChoiceForm />
            </Card>
          </>
        );
      case GameInitialisationStepEnum.DISPATCH:
        return null;
      case GameInitialisationStepEnum.FINISH:
        return null;
      default:
        return null;
    }
  }, [initialisationStep, openInvitation, closeInvitation, game, t, resetConfiguration]);

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
      <TooMuchPlayersDialog
        open={openTooMuchPlayersDialog}
        setOpen={setOpenTooMuchPlayersDialog}
        playableRoleList={tooMuchPlayersDialogData?.playableRoleList ?? []}
        currentRoles={tooMuchPlayersDialogData?.currentRoles ?? []}
        onCompositionChange={tooMuchPlayersDialogData?.onCompositionChange ?? (() => {})}
      />
    </div>
  );
};

export default NewGameDisplay;
