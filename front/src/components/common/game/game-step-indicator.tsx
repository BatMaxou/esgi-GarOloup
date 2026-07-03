import { useTranslations } from 'next-intl';
import { useGame } from '@/contexts/game-context';
import { GameRuntimeStepEnum } from '@/utils/enums';
import Card from '@/components/ui/molecules/card';
import Typography from '@/components/ui/atoms/typography';

const GameStepIndicator = () => {
  const { game } = useGame();
  const t = useTranslations('components.common.game.gameStepIndicator');
  if (!game) {
    return null;
  }

  const runtimeStep = game.runtimeStep;
  const nightCounter = game.nights?.length ?? 0;
  const dayCounter = game.days?.length ?? 0;
  const voteCounter = game.votes?.length ?? 0;

  if (runtimeStep === GameRuntimeStepEnum.SETUP) {
    return (
      <Card hoverable={false} orientation="horizontal" className="p-4! gap-2">
        <Typography tag="span" className="phase-icon">
          🏁
        </Typography>
        <Typography tag="span" className="phase-label">
          {t('setup')}
        </Typography>
      </Card>
    );
  }
  if (runtimeStep === GameRuntimeStepEnum.NIGHT) {
    return (
      <Card hoverable={false} orientation="horizontal" className="p-4! gap-2">
        <Typography tag="span" className="phase-icon">
          🌙
        </Typography>
        <Typography tag="span" className="phase-label">
          {t('night')}
        </Typography>
        <Typography tag="span" className="phase-day-num">
          — {t('round')} {nightCounter}
        </Typography>
      </Card>
    );
  }
  if (runtimeStep === GameRuntimeStepEnum.DAY) {
    return (
      <Card hoverable={false} orientation="horizontal" className="p-4! gap-2">
        <Typography tag="span" className="phase-icon">
          ☀️
        </Typography>
        <Typography tag="span" className="phase-label">
          {t('day')}
        </Typography>
        <Typography tag="span" className="phase-day-num">
          — {t('round')} {dayCounter}
        </Typography>
      </Card>
    );
  }
  if (runtimeStep === GameRuntimeStepEnum.VOTE) {
    return (
      <Card hoverable={false} orientation="horizontal" className="p-4! gap-2">
        <Typography tag="span" className="phase-icon">
          🗳
        </Typography>
        <Typography tag="span" className="phase-label">
          {t('vote')}
        </Typography>
        <Typography tag="span" className="phase-day-num">
          — {t('round')} {voteCounter}
        </Typography>
      </Card>
    );
  }
  if (runtimeStep === GameRuntimeStepEnum.INTERRUPT) {
    return (
      <Card hoverable={false} orientation="horizontal" className="p-4! gap-2">
        <Typography tag="span" className="phase-icon">
          ⭐️
        </Typography>
        <Typography tag="span" className="phase-label">
          {t('interrupted')}
        </Typography>
        <Typography tag="span" className="phase-day-num">
          — {t('round')} {game.interruptedRuntimeStep === GameRuntimeStepEnum.NIGHT ? nightCounter : voteCounter}
        </Typography>
      </Card>
    );
  }
  if (runtimeStep === GameRuntimeStepEnum.FINISH) {
    return (
      <Card hoverable={false} orientation="horizontal" className="p-4! gap-2">
        <Typography tag="span" className="phase-icon">
          🏁
        </Typography>
        <Typography tag="span" className="phase-label">
          {t('ended')}
        </Typography>
      </Card>
    );
  }
  return null;
};

export default GameStepIndicator;
