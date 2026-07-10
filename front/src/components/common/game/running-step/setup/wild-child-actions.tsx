'use client';

import { useMemo, useState } from 'react';
import { useTranslations } from 'next-intl';

import { usePlayer } from '@/contexts/player-context';
import { useGame } from '@/contexts/game-context';
import { useWildChild } from '@/contexts/roles/wild-child-context';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import { ApiClientError } from '@/lib/api/ApiClientError';
import type { Player } from '@/utils/types';

const getUsername = (gamePlayer?: Player) =>
  (gamePlayer?.user?.username || gamePlayer?.tempUser?.username || gamePlayer?.username) ?? '';

const WildChildActions = () => {
  const { player } = usePlayer();
  const { game } = useGame();
  const { modelPlayerId, setup } = useWildChild();
  const t = useTranslations('components.common.game.setupActions.wildChildActions');

  const [targetId, setTargetId] = useState<string | null>(modelPlayerId ?? null);
  const [submitting, setSubmitting] = useState(false);
  const [actionEnded, setActionEnded] = useState(Boolean(modelPlayerId));
  const [submittedModelName, setSubmittedModelName] = useState<string | null>(() => {
    if (!modelPlayerId || !game?.players) {
      return null;
    }
    const modelPlayer = game.players.find((gamePlayer) => gamePlayer.id === modelPlayerId);
    return modelPlayer ? getUsername(modelPlayer) : null;
  });

  const selectablePlayers = useMemo(
    () => game?.players?.filter((gamePlayer) => gamePlayer.id !== player?.id) ?? [],
    [game?.players, player?.id]
  );

  const handleSubmit = async () => {
    if (!targetId || submitting || actionEnded) {
      return;
    }

    setSubmitting(true);
    const response = await setup(targetId);
    if (response instanceof ApiClientError) {
      setSubmitting(false);
      return;
    }

    const modelPlayer = game?.players?.find((gamePlayer) => gamePlayer.id === targetId);
    setSubmittedModelName(modelPlayer ? getUsername(modelPlayer) : null);
    setActionEnded(true);
    setSubmitting(false);
  };

  if (actionEnded) {
    return (
      <div className="flex flex-col gap-8">
        <Card hoverable={false} orientation="horizontal" className="gap-6 items-center">
          <Icon name="wildChild" className="w-10 h-10" />
          <div className="flex flex-col gap-2">
            <Typography tag="h3" variant="subtitle" bold>
              {t('wildChild')}
            </Typography>
            <Typography tag="span" className="text-sm text-success!" bold>
              {t('wildChildCamp')}
            </Typography>
          </div>
        </Card>
        <div className="flex flex-col justify-center items-center gap-2">
          <Typography tag="p" variant="body">
            {t('actionEnded')}
          </Typography>
          {submittedModelName && (
            <Typography tag="p" variant="body-sm" className="text-primary/80">
              {t('modelChosen', { username: submittedModelName })}
            </Typography>
          )}
        </div>
      </div>
    );
  }

  return (
    <div className="flex flex-col gap-8 h-full">
      <Card hoverable={false} orientation="horizontal" className="gap-6 items-center">
        <Icon name="wildChild" className="w-10 h-10" />
        <div className="flex flex-col gap-2">
          <Typography tag="h3" variant="subtitle" bold>
            {t('wildChild')}
          </Typography>
          <Typography tag="span" className="text-sm text-success!" bold>
            {t('wildChildCamp')}
          </Typography>
        </div>
      </Card>

      <Typography tag="p" variant="body">
        {t('roundDescription')}
      </Typography>

      <div className="grid grid-cols-3 auto-rows-min gap-3 w-full flex-1 min-h-0 overflow-y-auto scrollbar">
        {selectablePlayers.map((gamePlayer) => {
          const isTarget = targetId === gamePlayer.id;

          return (
            <Card
              key={gamePlayer.id}
              onClick={() => setTargetId(gamePlayer.id)}
              isCurrentPlayer={isTarget}
              liftOnHover={false}
              hoverable={!isTarget}
              className={`relative flex flex-col items-center justify-center gap-3 min-h-32 border! cursor-pointer
                ${
                  isTarget
                    ? 'border-error! bg-error/20!'
                    : 'border-primary/15! hover:bg-primary/10 hover:border-primary/50!'
                }
                ${submitting ? 'opacity-60 pointer-events-none' : ''}
              `}
            >
              <Typography variant="body" textColor="light" center>
                {getUsername(gamePlayer)}
              </Typography>
            </Card>
          );
        })}
      </div>

      <Button
        type="button"
        onClick={handleSubmit}
        variant="gradient"
        className="w-full"
        disabled={!targetId || submitting}
        loading={submitting}
        label={t('submit')}
      />
    </div>
  );
};

export default WildChildActions;
