'use client';

import { useTranslations } from 'next-intl';

import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';

const ITEMS_META = [
  { icon: '🎭', featured: true as const },
  { icon: '⚡', featured: false as const },
  { icon: '🎲', featured: false as const },
  { icon: '🌐', featured: false as const },
  { icon: '🆓', featured: false as const },
  { icon: '🛠️', featured: false as const },
] as const;

const iconClassName = 'mb-3.5 block text-[1.8rem]';

const WhyUs = () => {
  const t = useTranslations('components.common.home.whyUs');

  return (
    <section id="pourquoi" className="px-6 py-[100px]">
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
        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
          {ITEMS_META.map(({ icon, featured }, index) => (
            <Card key={index} variant={featured ? 'accent' : 'none'} className="px-6 py-7">
              <span className={iconClassName} aria-hidden>
                {icon}
              </span>
              <Typography
                tag="h3"
                className={
                  featured
                    ? 'mb-2 font-title text-[0.95rem] font-bold text-accent!'
                    : 'mb-2 font-title text-[0.95rem] font-bold text-white'
                }
              >
                {t(`items.${index}.title`)}
              </Typography>
              <Typography tag="p" className="text-[0.82rem] leading-[1.65] text-neutral-500">
                {t(`items.${index}.body`)}
              </Typography>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default WhyUs;
