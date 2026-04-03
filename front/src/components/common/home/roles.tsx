'use client';

import { useTranslations } from 'next-intl';

import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import { paths } from '@/utils/paths';

const badgeClassName =
  'absolute top-3 right-3 rounded-xxs border border-accent/30 bg-accent/15 px-[7px] py-0.5 text-[0.8rem]! font-bold tracking-[0.08em] text-accent! uppercase';

const ROLE_CARDS = [
  { variant: 'werewolf' as const, emoji: '🐺', key: 'werewolf' as const },
  { variant: 'solo' as const, emoji: '🧙‍♀️', key: 'witch' as const },
  { variant: 'village' as const, emoji: '🏹', key: 'hunter' as const },
  { variant: 'couple' as const, emoji: '🎭', key: 'gm' as const },
] as const;

const campClassName: Record<(typeof ROLE_CARDS)[number]['variant'], string> = {
  werewolf: 'mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-error! uppercase',
  solo: 'mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-success uppercase',
  village: 'mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-success uppercase',
  couple: 'mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-accent! uppercase',
};

const Roles = () => {
  const t = useTranslations('components.common.home.roles');

  return (
    <section id="roles" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[1100px]">
        <Typography tag="div" textColor="primary" bold>
          {t('eyebrow')}
        </Typography>
        <Typography tag="h2" special variant="heading-2" bold>
          {t('title')}
        </Typography>
        <Typography tag="p" className="mb-10">
          {t('subtitle')}
        </Typography>
        <div className="mb-9 grid grid-cols-2 gap-4 md:grid-cols-4">
          {ROLE_CARDS.map(({ variant, emoji, key: cardKey }) => (
            <Card key={cardKey} type="role" variant={variant}>
              <Typography tag="span" className={badgeClassName}>
                {t('newBadge')}
              </Typography>
              <Typography tag="span" className="mt-8 mb-3.5 block text-[2.2rem]">
                {emoji}
              </Typography>
              <Typography tag="div" special className="mb-1.5 text-xl text-white">
                {t(`cards.${cardKey}.name`)}
              </Typography>
              <Typography tag="div" className={campClassName[variant]}>
                {t(`cards.${cardKey}.camp`)}
              </Typography>
              <Typography tag="p" className="text-[0.8rem] leading-[1.6] text-neutral-500">
                {t(`cards.${cardKey}.description`)}
              </Typography>
            </Card>
          ))}
        </div>
        <Button asLink href={paths.roles} variant={'secondary'} glass label={t('cta') + ' →'} />
      </div>
    </section>
  );
};

export default Roles;
