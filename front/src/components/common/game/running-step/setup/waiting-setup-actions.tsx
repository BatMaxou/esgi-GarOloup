import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { useTranslations } from 'next-intl';

const WaitingSetupActions = () => {
  const t = useTranslations('components.common.game.waitingSetupActions');

  return (
    <div className="flex flex-col items-center justify-center gap-8 h-full w-full">
      <Icon name="garoloup" className="animate-pulse w-20 h-20" />
      <div className="flex flex-col items-center justify-center gap-2">
        <Typography variant="subtitle" className="animate-pulse">
          {t('waitingSetup')}
        </Typography>
      </div>
    </div>
  );
};

export default WaitingSetupActions;
