'use client';

import { useMemo, useState } from 'react';
import { useTranslations } from 'next-intl';

import { useGame } from '@/contexts/game-context';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import { ApiClientError } from '@/lib/api/ApiClientError';
import type { Player } from '@/utils/types';
import { useCupidon } from '@/contexts/roles/cupidon-context';

const getUsername = (gamePlayer?: Player) =>
  (gamePlayer?.user?.username || gamePlayer?.tempUser?.username || gamePlayer?.username) ?? '';

const CupidonActions = () => {
  const { game } = useGame();
  const { setup, hasFormedCouple } = useCupidon();
  const t = useTranslations('components.common.game.setupActions.cupidonActions');

  const [firstLoverId, setFirstLoverId] = useState<string | null>(null);
  const [secondLoverId, setSecondLoverId] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const selectablePlayers = useMemo(() => game?.players ?? [], [game?.players]);

  const handleSubmit = async () => {
    if (!firstLoverId || !secondLoverId || submitting) {
      return;
    }

    setSubmitting(true);
    const response = await setup(firstLoverId, secondLoverId);
    if (response instanceof ApiClientError) {
      setSubmitting(false);
      return;
    }

    setSubmitting(false);
  };

  const handleLoverSelection = (loverId: string) => {
    // Vérification pour annuler la selection d'un joueur si celui cliqué est choisi
    if (firstLoverId === loverId) {
      setFirstLoverId(null);
    } else if (secondLoverId === loverId) {
      setSecondLoverId(null);
    } else {
      if (firstLoverId) {
        setSecondLoverId(loverId);
      } else {
        setFirstLoverId(loverId);
      }
    }
  };

  return (
    <div className="flex flex-col gap-8 h-full">
      <Card hoverable={false} orientation="horizontal" className="gap-6 items-center">
        <Icon name="cupidon" className="w-10 h-10" />
        <div className="flex flex-col gap-2">
          <Typography tag="h3" variant="subtitle" bold>
            {t('cupidon')}
          </Typography>
          <Typography tag="span" className="text-sm text-success!" bold>
            {t('cupidonCamp')}
          </Typography>
        </div>
      </Card>
      {hasFormedCouple ? (
        <>
          <Typography variant="subtitle">{t('alreadyShotTitle')}</Typography>
          <Typography variant="body">{t('alreadyShotSubtitle')}</Typography>
        </>
      ) : (
        <>
          <Typography tag="p" variant="body">
            {t('roundDescription')}
          </Typography>

          <div className="grid grid-cols-3 auto-rows-min gap-3 w-full flex-1 min-h-0 overflow-y-auto scrollbar">
            {selectablePlayers.map((gamePlayer) => {
              const isFirstLover = firstLoverId === gamePlayer.id;
              const isSecondLover = secondLoverId === gamePlayer.id;

              return (
                <Card
                  key={gamePlayer.id}
                  onClick={() => handleLoverSelection(gamePlayer.id)}
                  isCurrentPlayer={isFirstLover || isSecondLover}
                  liftOnHover={false}
                  hoverable={!isFirstLover && !isSecondLover}
                  className={`relative flex flex-col items-center justify-center gap-3 min-h-32 border! cursor-pointer
                  ${
                    isFirstLover || isSecondLover
                      ? 'border-pink-500! bg-pink-500/20!'
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
            disabled={!firstLoverId || !secondLoverId || submitting}
            loading={submitting}
            label={t('submit')}
          />
        </>
      )}
    </div>
  );
};

export default CupidonActions;
