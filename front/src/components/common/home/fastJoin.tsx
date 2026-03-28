'use client';

import { useTranslations } from 'next-intl';

import Dot from '@/components/ui/atoms/dot';
import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import Card from '@/components/ui/molecules/card';

const GAME_INDICES = [0, 1, 2] as const;

const GAME_FILL: Record<(typeof GAME_INDICES)[number], number> = {
  0: 50,
  1: 37,
  2: 90,
};

const gameCardClassName =
  'items-center justify-between gap-4 !rounded-xs !border-primary/13 !bg-[rgba(26,28,46,0.55)] !px-5 !py-4 backdrop-blur-md duration-200 hover:!border-primary/30 hover:!bg-[rgba(62,65,90,0.45)] hover:!shadow-none';

const FastJoin = () => {
  const t = useTranslations('components.common.home.fastJoin');

  return (
    <section id="parties" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[1100px]">
        <Typography
          tag="div"
          className="mb-2.5 flex items-center gap-2 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase"
        >
          <Dot variant="live" />
          {t('eyebrow')}
        </Typography>
        <Typography tag="h2" special className="mb-4 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          {t('title')}
        </Typography>
        <Typography tag="p" className="mb-12 max-w-[560px] text-base leading-[1.7] text-neutral-400">
          {t('subtitle')}
        </Typography>
        <div className="mb-8 flex flex-col gap-2.5">
          {GAME_INDICES.map((index) => (
            <Card key={index} liftOnHover={false} orientation="horizontal" className={gameCardClassName}>
              <div className="flex min-w-0 flex-1 items-center gap-4">
                <Dot variant="live" />
                <Typography tag="div" special className="min-w-[72px] shrink-0 text-lg text-accent! text-glow-accent">
                  {t(`games.${index}.code`)}
                </Typography>
                <div className="min-w-0">
                  <Typography tag="div" className="text-[0.85rem] font-semibold text-neutral-200">
                    {t(`games.${index}.host`)}
                  </Typography>
                  <Typography tag="div" className="mt-0.5 text-[0.75rem] text-neutral-500">
                    {t(`games.${index}.players`)}
                  </Typography>
                  <div className="mt-1.5 h-1 w-20 overflow-hidden rounded-lg bg-primary/15" role="presentation">
                    <div
                      className="h-full rounded-lg bg-linear-(--primary-gradient)"
                      style={{ width: `${GAME_FILL[index]}%` }}
                    />
                  </div>
                </div>
              </div>
              <Button variant="gradient" size="sm" label={t('joinButton')} />
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

export default FastJoin;
