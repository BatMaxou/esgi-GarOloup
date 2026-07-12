'use client';

import { useTranslations } from 'next-intl';
import { motion } from 'motion/react';

import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { IconName } from '@/components/ui/atoms/icon/config';
import { RecapBeat } from './recap-beats';

const BEAT_ICONS: Record<RecapBeat['type'], IconName> = {
  death: 'skull',
  calm: 'moon',
};

const NightRecap = ({ beat }: { beat: RecapBeat }) => {
  const t = useTranslations('components.common.game.nightRecap');

  const renderMessage = () => {
    switch (beat.type) {
      case 'death':
        return beat.username ? t('death', { username: beat.username }) : t('deathUnknown');
      case 'calm':
      default:
        return t('calm');
    }
  };

  return (
    <div className="fixed inset-0 z-9999 flex flex-col items-center justify-center gap-6 bg-black/50 backdrop-blur-sm">
      <motion.div
        key={`${beat.id}-icon`}
        initial={{ opacity: 0 }}
        animate={{ opacity: [0.4, 1, 0.4] }}
        exit={{ opacity: 0 }}
        transition={{ duration: 2.5, ease: 'easeInOut' }}
      >
        <Icon name={BEAT_ICONS[beat.type]} className="w-16 h-16 text-foreground" />
      </motion.div>
      <motion.div
        key={`${beat.id}-text`}
        initial={{ opacity: 0 }}
        animate={{ opacity: [0.4, 1, 0.4] }}
        exit={{ opacity: 0 }}
        transition={{ duration: 2.5, ease: 'easeInOut' }}
      >
        <Typography tag="p" variant="subtitle" bold center>
          {renderMessage()}
        </Typography>
      </motion.div>
    </div>
  );
};

export default NightRecap;
