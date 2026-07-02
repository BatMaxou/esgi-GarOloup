import { useTranslations } from 'next-intl';
import { useFormik } from 'formik';

import { usePlayer } from '@/contexts/player-context';
import { useGame } from '@/contexts/game-context';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import { useWerewolf } from '@/contexts/roles/werewolf-context';

const WerewolfActions = () => {
  const { player } = usePlayer();
  const { game } = useGame();
  const { team, vote } = useWerewolf();
  const t = useTranslations('components.common.game.nightActions.werewolfActions');

  const playerList =
    game?.players
      ?.filter(
        (gamePlayer) =>
          player?.id !== gamePlayer.id &&
          !gamePlayer.dead &&
          !team?.members.some((member) => member.id === gamePlayer.id)
      )
      .map((gamePlayer) => ({
        id: gamePlayer.id,
        name: (gamePlayer.user?.username || gamePlayer.tempUser?.username) ?? '',
      })) ?? [];

  const isSpectator = game?.players?.some((currentPlayer) => currentPlayer.id === player?.id && currentPlayer.dead);

  const { handleSubmit, handleChange, values } = useFormik({
    initialValues: {
      targetId: '',
    },
    onSubmit: async (values) => {
      if (isSpectator) {
        return;
      }
      await vote(values.targetId);
    },
  });

  const handleSelectPlayer = (playerId: string) => {
    if (isSpectator) {
      return;
    }
    if (values.targetId === playerId) {
      handleChange(null);
      return;
    }
    handleChange({ target: { name: 'targetId', value: playerId } });
  };

  return (
    <div className="flex flex-col gap-8">
      <Card hoverable={false} orientation="horizontal" className="gap-6 items-center">
        <Icon name="werewolf" className="w-10 h-10" />
        <div className="flex flex-col gap-2">
          <Typography tag="h3" variant="subtitle" bold>
            {t('werewolf')}
          </Typography>
          <Typography tag="span" className="text-sm text-error!" bold>
            {t('werewolfCamp')}
          </Typography>
        </div>
      </Card>
      <Typography tag="p" variant="body">
        {t('roundDescription')}
      </Typography>
      <Card hoverable={false}>
        <Typography tag="span" className="phase-icon">
          {t('actionLabel')}
        </Typography>

        <div className="flex flex-col gap-2 pr-2 overflow-y-scroll scrollbar max-h-96">
          {playerList.map((player) => (
            <Card
              key={player.id}
              onClick={isSpectator ? undefined : () => handleSelectPlayer(player.id)}
              isCurrentPlayer={values.targetId === player.id}
              liftOnHover={false}
              className={`flex flex-row border! w-full 
                ${isSpectator ? 'cursor-default' : 'cursor-pointer'}
                ${
                  values.targetId === player.id
                    ? 'justify-between items-between! border-error! bg-error/20!'
                    : 'items-center! border-primary/15! justify-start! hover:bg-error/10 hover:border-error/50!'
                }
              `}
              hoverable={values.targetId !== player.id && !isSpectator}
            >
              <Typography variant="body" textColor="light">
                {player.name}
              </Typography>
              {values.targetId === player.id && (
                <Typography variant="body" textColor="error" className="flex items-center gap-2">
                  <Icon name="skull" className="w-4 h-4 color-error" />
                  {t('focused')}
                </Typography>
              )}
            </Card>
          ))}
        </div>
      </Card>
      <Button
        type="submit"
        onClick={() => handleSubmit()}
        variant="gradient"
        className="w-full"
        disabled={!values.targetId || isSpectator}
        label={t('submit')}
      />
    </div>
  );
};

export default WerewolfActions;
