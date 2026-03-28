'use client';

import { useState } from 'react';
import { useTranslations } from 'next-intl';

import Typography from '@/components/ui/atoms/typography';
import Dropdown from '@/components/ui/molecules/dropdown';

const FAQ_IDS = ['players-min', 'account', 'duration', 'gm-mode', 'irl'] as const;

const Faq = () => {
  const [openId, setOpenId] = useState<string | null>(null);
  const t = useTranslations('components.common.home.faq');

  return (
    <section id="faq" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[720px]">
        <Typography tag="div" className="mb-2.5 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase">
          {t('eyebrow')}
        </Typography>
        <Typography tag="h2" special className="mb-10 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          {t('title')}
        </Typography>
        <div className="mb-9 flex flex-col gap-2">
          {FAQ_IDS.map((id) => (
            <Dropdown
              key={id}
              open={openId === id}
              onOpenChange={(next) => setOpenId(next ? id : null)}
              trigger={t(`items.${id}.question`)}
            >
              {t(`items.${id}.answer`)}
            </Dropdown>
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

export default Faq;
