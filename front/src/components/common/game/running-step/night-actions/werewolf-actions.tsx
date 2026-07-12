import { useTranslations } from 'next-intl';
import { useFormik } from 'formik';

import { usePlayer } from '@/contexts/player-context';
import { useGame } from '@/contexts/game-context';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import { useWerewolf } from '@/contexts/roles/werewolf-context';
import { isLoverRole, useOptionalLover } from '@/contexts/roles/lover-context';
import PlayerLoverPartnerIcon from '@/components/ui/molecules/icon/player-lover-partner-item';

const WerewolfActions = () => {
  const { player } = usePlayer();
  const { game } = useGame();
  const { team, vote } = useWerewolf();
  const t = useTranslations('components.common.game.nightActions.werewolfActions');
  const { partnerPlayerId: loverPartnerId } = useOptionalLover();
  const partnerPlayerId = loverPartnerId ?? (isLoverRole(player?.role) ? (player?.role.partnerPlayerId ?? null) : null);
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

  const teammates = team?.members.filter((member) => member.id !== player?.id) ?? [];

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
      {teammates.length > 0 && (
        <Card hoverable={false}>
          <Typography tag="span" className="phase-icon">
            {t('teamLabel')}
          </Typography>
          <div className="flex flex-row flex-wrap gap-x-6 gap-y-2">
            {teammates.map((member) => (
              <Typography key={member.id} variant="body" textColor="light" className="flex items-center gap-2">
                <Icon name="werewolf" className="w-4 h-4 color-error" />
                {member.username}
              </Typography>
            ))}
          </div>
        </Card>
      )}
      <Card hoverable={false}>
        <Typography tag="span" className="phase-icon">
          {t('actionLabel')}
        </Typography>

        <div className="flex flex-col gap-2 pr-2 overflow-y-scroll scrollbar max-h-96">
          {playerList.map((gamePlayer) => (
            <Card
              key={gamePlayer.id}
              onClick={
                isSpectator || (isLoverRole(player?.role) && gamePlayer.id === partnerPlayerId)
                  ? undefined
                  : () => handleSelectPlayer(gamePlayer.id)
              }
              isCurrentPlayer={values.targetId === gamePlayer.id}
              liftOnHover={false}
              className={`flex flex-row border! w-full 
                ${isSpectator ? 'cursor-default' : 'cursor-pointer'}
                ${
                  values.targetId === gamePlayer.id
                    ? 'justify-between items-between! border-error! bg-error/20!'
                    : isLoverRole(player?.role) && gamePlayer.id === partnerPlayerId
                      ? 'justify-between items-between! border-pink-700! bg-pink-700/20! cursor-default!'
                      : 'items-center! border-primary/15! justify-start! hover:bg-error/10 hover:border-error/50!'
                }
              `}
              hoverable={values.targetId !== gamePlayer.id && !isSpectator}
            >
              <Typography variant="body" textColor="light">
                {gamePlayer.name}{' '}
              </Typography>
              {values.targetId === gamePlayer.id && (
                <Typography variant="body" textColor="error" className="flex items-center gap-2">
                  <Icon name="skull" className="w-4 h-4 color-error" />
                  {t('focused')}
                </Typography>
              )}
              {isLoverRole(player?.role) && gamePlayer.id === partnerPlayerId && (
                <Typography variant="body" textColor="controlled" className="flex items-center gap-2 text-pink-700">
                  <PlayerLoverPartnerIcon player={gamePlayer} className="w-4 h-4 color-pink-700" />
                  {t('loverPartner')}
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
