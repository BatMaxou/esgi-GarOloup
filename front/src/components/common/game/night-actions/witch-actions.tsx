import { useTranslations } from 'next-intl';
import { useFormik } from 'formik';

import { usePlayer } from '@/contexts/player-context';
import { useGame } from '@/contexts/game-context';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import { useWitch } from '@/contexts/roles/witch-context';
import { useState } from 'react';
import Tag from '@/components/ui/molecules/tag';

const WitchActions = () => {
  const { player } = usePlayer();
  const { game } = useGame();
  const { healPotionAvailable, poisonPotionAvailable, save, poison } = useWitch();
  const t = useTranslations('components.common.game.nightActions.witchActions');
  const [actionType, setActionType] = useState<'save' | 'poison' | null>(null);
  const [killStep, setKillStep] = useState(false);
  const [poisonTargetId, setPoisonTargetId] = useState<string | null>(null);
  const playerAliveList =
    game?.players
      ?.filter((gamePlayer) => player?.id !== gamePlayer.id && !gamePlayer.dead)
      .map((gamePlayer) => ({
        id: gamePlayer.id,
        name: gamePlayer.user?.username ?? '',
      })) ?? [];

  const { handleSubmit, handleChange } = useFormik({
    initialValues: {
      type: '',
      targetId: '',
    },
    onSubmit: async (values) => {
      if (values.type === 'save') {
        await save(values.targetId);
      } else if (values.type === 'poison') {
        await poison(values.targetId);
      }
    },
  });

  const handleSelectActionType = (type: 'save' | 'poison' | null) => {
    if (actionType === type) {
      setActionType(null);
      return;
    }
    setActionType(type);
  };

  const handleSubmitChoice = () => {
    if (actionType === 'save') {
      handleChange({ target: { name: 'targetId', value: null } }); // Pas encore effectif, en attente de récupération du joueur tué par les loups
      handleChange({ target: { name: 'type', value: 'save' } });
      handleSubmit();
    } else if (actionType === 'poison' && !killStep) {
      setKillStep(true);
    } else if (killStep) {
      handleChange({ target: { name: 'targetId', value: poisonTargetId } });
      handleChange({ target: { name: 'type', value: 'poison' } });
      handleSubmit();
    }
  };

  const handleSelectPoisonTarget = (playerId: string) => {
    if (poisonTargetId === playerId) {
      setPoisonTargetId(null);
      return;
    }
    setPoisonTargetId(playerId);
  };

  return (
    <div className="flex flex-col gap-8">
      <Card hoverable={false} orientation="horizontal" className="gap-6 items-center">
        <Icon name="werewolf" className="w-10 h-10" />
        <div className="flex flex-col gap-2">
          <Typography tag="h3" variant="subtitle" bold>
            {t('witch')}
          </Typography>
          <Typography tag="span" className="text-sm text-success!" bold>
            {t('witchCamp')}
          </Typography>
        </div>
      </Card>
      <Typography tag="p" variant="body">
        {t('roundDescription')}
      </Typography>
      <div className="flex flex-row justify-center gap-2">
        {!killStep ? (
          <>
            <Card
              liftOnHover={false}
              className={`flex flex-col gap-2 items-center cursor-pointer ${actionType === 'save' ? 'border-success/40!' : 'm-0.5'}`}
              onClick={() => handleSelectActionType('save')}
            >
              <Icon name="heal" className="w-10 h-10 text-success/60" />
              <Typography tag="p" variant="body" className="font-bold">
                {t('saveTitle')}
              </Typography>
              <Typography tag="span" variant="body-sm" className="text-primary/60">
                {t('saveDescription')}
              </Typography>
              <Tag
                label={healPotionAvailable ? t('healTag') : t('healTagDisabled')}
                variant={healPotionAvailable ? 'success' : 'neutral'}
                disabled={!healPotionAvailable}
                size="lg"
              />
            </Card>
            <Card
              liftOnHover={false}
              className={`flex flex-col gap-2 items-center cursor-pointer ${actionType === 'poison' ? 'border-error/40! border-2' : 'm-0.5'}`}
              onClick={() => handleSelectActionType('poison')}
            >
              <Icon name="poison" className="w-10 h-10 text-error/60" />
              <Typography tag="span" variant="body" className="font-bold">
                {t('poisonTitle')}
              </Typography>
              <Typography tag="span" variant="body-sm" className="text-primary/60">
                {t('poisonDescription')}
              </Typography>
              <Tag
                label={poisonPotionAvailable ? t('poisonTag') : t('poisonTagDisabled')}
                variant={poisonPotionAvailable ? 'error' : 'neutral'}
                disabled={!poisonPotionAvailable}
                size="lg"
              />
            </Card>
          </>
        ) : (
          <Card liftOnHover={false} className="flex flex-col gap-2 items-center cursor-pointer w-full ">
            <div className="flex flex-row gap-2 items-center">
              <Icon name="poison" className="w-10 h-10 text-error/60" />
              <Typography tag="span" variant="body" className="font-bold">
                {t('poisonChoiceTitle')}
              </Typography>
            </div>
            <div className="flex flex-col gap-2 pr-2 w-full overflow-y-scroll scrollbar max-h-96">
              {playerAliveList.map((player) => (
                <Card
                  key={player.id}
                  onClick={() => handleSelectPoisonTarget(player.id)}
                  isCurrentPlayer={poisonTargetId === player.id}
                  liftOnHover={false}
                  className={`cursor-pointer flex flex-row border! w-full
                    ${
                      poisonTargetId === player.id
                        ? 'justify-between items-between! border-error! bg-error/20! gap-12'
                        : 'items-center! border-primary/15! justify-start! hover:bg-error/10 hover:border-error/50!'
                    }
                  `}
                  hoverable={poisonTargetId !== player.id}
                >
                  <Typography variant="body" textColor="light">
                    {player.name}
                  </Typography>
                  {poisonTargetId === player.id && (
                    <Typography variant="body" textColor="error" className="flex items-center gap-2">
                      <Icon name="skull" className="w-4 h-4 color-error" />
                      {t('poisonTarget')}
                    </Typography>
                  )}
                </Card>
              ))}
            </div>
          </Card>
        )}
      </div>
      <Button
        type={actionType === 'save' || killStep ? 'submit' : 'button'}
        onClick={() => handleSubmitChoice()}
        variant="gradient"
        className="w-full"
        disabled={!actionType || (killStep && !poisonTargetId)}
        label={killStep ? t('submitTarget') : t('submitChoice')}
      />
    </div>
  );
};

export default WitchActions;
