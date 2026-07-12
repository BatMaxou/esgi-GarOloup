'use client';

import { useTranslations } from 'next-intl';
import { motion } from 'motion/react';

import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { roleIcon } from '@/components/pages/recap/config';
import { usePlayer } from '@/contexts/player-context';
import { useGame } from '@/contexts/game-context';

import { useSeerReveal } from './use-seer-reveal';

const SeerRevealAnimation = () => {
  const t = useTranslations('components.common.game.nightRecap');
  const { game } = useGame();
  const { player } = usePlayer();
  const { isPlaying, currentBeat } = useSeerReveal(game, player);

  if (!isPlaying || !currentBeat || currentBeat.type !== 'reveal') {
    return null;
  }

  const revealedRoleIcon = currentBeat.role ? (roleIcon[currentBeat.role] ?? 'questionMark') : 'questionMark';

  return (
    <div className="fixed inset-0 z-9999 flex flex-col items-center justify-center gap-6 bg-black/50 backdrop-blur-sm">
      <motion.div
        key={`${currentBeat.id}-icon`}
        initial={{ opacity: 0 }}
        animate={{ opacity: [0.4, 1, 0.4] }}
        exit={{ opacity: 0 }}
        transition={{ duration: 2.5, ease: 'easeInOut' }}
      >
        <Icon name={revealedRoleIcon} className="w-16 h-16 text-foreground" />
      </motion.div>
      <motion.div
        key={`${currentBeat.id}-text`}
        initial={{ opacity: 0 }}
        animate={{ opacity: [0.4, 1, 0.4] }}
        exit={{ opacity: 0 }}
        transition={{ duration: 2.5, ease: 'easeInOut' }}
      >
        <Typography tag="p" variant="subtitle" bold center>
          {t('reveal', {
            username: currentBeat.username ?? '',
            role: currentBeat.role ? t(`roles.${currentBeat.role}`) : '',
          })}
        </Typography>
      </motion.div>
    </div>
  );
};

export default SeerRevealAnimation;
