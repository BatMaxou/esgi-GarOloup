'use client';

import { useTranslations } from 'next-intl';

import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';

const STEPS_META = [
  { num: 1, icon: '🌙' },
  { num: 2, icon: '☀️' },
  { num: 3, icon: '🗣️' },
  { num: 4, icon: '🗳️' },
] as const;

const stepNumClassName =
  'flex h-10 min-w-10 shrink-0 items-center justify-center rounded-full bg-linear-(--primary-gradient) font-special text-[1.1rem] text-white shadow-[0_0_16px_rgba(159,154,214,0.25)]';

const Rules = () => {
  const t = useTranslations('components.common.home.rules');

  return (
    <section id="regles" className="px-6 py-[100px]">
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
        <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
          {STEPS_META.map(({ num, icon }, index) => (
            <Card key={num} orientation="horizontal" className="items-center gap-[18px]">
              <div className={stepNumClassName} aria-hidden>
                {num}
              </div>
              <div className="min-w-0 flex-1">
                <Typography tag="h3" className="mb-1.5 font-title text-[0.95rem] font-bold text-white">
                  <span className="mr-1.5 inline-block align-middle text-[1.1rem]">{icon}</span>
                  {t(`steps.${index}.title`)}
                </Typography>
                <Typography tag="p" className="text-[0.82rem] leading-[1.6] text-neutral-500">
                  {t(`steps.${index}.body`)}
                </Typography>
              </div>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Rules;
