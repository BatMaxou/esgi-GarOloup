'use client';

import { useTranslations } from 'next-intl';

import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';

const badgeClassName =
  'absolute top-3 right-3 rounded-xxs border border-accent/30 bg-accent/15 px-[7px] py-0.5 text-[0.8rem]! font-bold tracking-[0.08em] text-accent! uppercase';

const ROLE_CARDS = [
  { variant: 'loup' as const, emoji: '🐺', key: 'werewolf' as const },
  { variant: 'sorciere' as const, emoji: '🧙‍♀️', key: 'witch' as const },
  { variant: 'villageois' as const, emoji: '🏹', key: 'hunter' as const },
  { variant: 'gm' as const, emoji: '🎭', key: 'gm' as const },
] as const;

const campClassName: Record<(typeof ROLE_CARDS)[number]['variant'], string> = {
  loup: 'mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-error! uppercase',
  sorciere: 'mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-success uppercase',
  villageois: 'mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-success uppercase',
  gm: 'mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-accent! uppercase',
};

const Roles = () => {
  const t = useTranslations('components.common.home.roles');

  return (
    <section id="roles" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[1100px]">
        <Typography tag="div" className="mb-2.5 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase">
          {t('eyebrow')}
        </Typography>
        <Typography tag="h2" special className="mb-4 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          {t('title')}
        </Typography>
        <Typography tag="p" className="font-semibold mb-12 max-w-[560px] text-base leading-[1.7] text-neutral-400">
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
        <a
          href="#"
          className="group/cta inline-flex cursor-pointer items-center gap-2 rounded-sm border border-primary/30 bg-transparent px-6 py-[11px] text-[0.88rem] font-bold tracking-[0.04em] text-primary-pastel no-underline transition-all duration-200 hover:translate-x-[3px] hover:border-primary/55 hover:bg-primary/10"
        >
          <Typography tag="span" textColor="controlled" className="font-bold">
            {t('cta')}{' '}
            <span className="inline-block transition-transform duration-200 group-hover/cta:translate-x-1">→</span>
          </Typography>
        </a>
      </div>
    </section>
  );
};

export default Roles;
