import { useTranslations } from 'next-intl';
import { useFormik } from 'formik';

import { usePlayer } from '@/contexts/player-context';
import { useGame } from '@/contexts/game-context';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import { useState } from 'react';
import { useSeer } from '@/contexts/roles/seer-context';

const SeerActions = () => {
  const { player } = usePlayer();
  const { game } = useGame();
  const { reveal } = useSeer();
  const t = useTranslations('components.common.game.nightActions.seerActions');
  const [revealTargetId, setRevealTargetId] = useState<string | null>(null);
  const playerAliveList =
    game?.players
      ?.filter((gamePlayer) => player?.id !== gamePlayer.id && !gamePlayer.dead)
      .map((gamePlayer) => ({
        id: gamePlayer.id,
        name: gamePlayer.user?.username ?? '',
      })) ?? [];

  const { handleSubmit, handleChange } = useFormik({
    initialValues: {
      targetId: '',
    },
    onSubmit: async (values) => {
      await reveal(values.targetId);
    },
  });

  const handleSubmitChoice = () => {
    handleChange({ target: { name: 'targetId', value: revealTargetId } });
    handleSubmit();
  };

  const handleSelectRevealTarget = (playerId: string) => {
    if (revealTargetId === playerId) {
      setRevealTargetId(null);
      return;
    }
    setRevealTargetId(playerId);
  };

  return (
    <div className="flex flex-col gap-8">
      <Card hoverable={false} orientation="horizontal" className="gap-6 items-center">
        <Icon name="seer" className="w-10 h-10" />
        <div className="flex flex-col gap-2">
          <Typography tag="h3" variant="subtitle" bold>
            {t('seer')}
          </Typography>
          <Typography tag="span" className="text-sm text-success!" bold>
            {t('seerCamp')}
          </Typography>
        </div>
      </Card>
      <Typography tag="p" variant="body">
        {t('roundDescription')}
      </Typography>
      <div className="flex flex-row justify-center gap-2">
        <Card liftOnHover={false} className="flex flex-col gap-2 items-start w-full ">
          <div className="flex flex-row gap-2">
            <Icon name="seer" className="w-6 h-6 text-primary/60" />
            <Typography tag="span" variant="body" className="font-bold">
              {t('revealChoiceTitle')}
            </Typography>
          </div>
          <div className="flex flex-col gap-2 pr-2 w-full overflow-y-scroll scrollbar max-h-96">
            {playerAliveList.map((player) => (
              <Card
                key={player.id}
                onClick={() => handleSelectRevealTarget(player.id)}
                isCurrentPlayer={revealTargetId === player.id}
                liftOnHover={false}
                className={`cursor-pointer flex flex-row border! w-full
                  ${
                    revealTargetId === player.id
                      ? 'justify-between items-between! border-primary! bg-primary/20! gap-12'
                      : 'items-center! border-primary/15! justify-start! hover:bg-primary/10 hover:border-primary/50!'
                  }
                `}
                hoverable={revealTargetId !== player.id}
              >
                <Typography variant="body" textColor="light">
                  {player.name}
                </Typography>
                {revealTargetId === player.id && (
                  <Typography variant="body" textColor="primary" className="flex items-center gap-2">
                    <Icon name="skull" className="w-4 h-4 color-primary" />
                    {t('revealTarget')}
                  </Typography>
                )}
              </Card>
            ))}
          </div>
        </Card>
      </div>
      <Button
        type="submit"
        onClick={() => handleSubmitChoice()}
        variant="gradient"
        className="w-full"
        disabled={!revealTargetId}
        label={t('submitChoice')}
      />
    </div>
  );
};

export default SeerActions;
