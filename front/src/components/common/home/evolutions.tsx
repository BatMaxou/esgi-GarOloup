'use client';

import { useState } from 'react';
import { useTranslations } from 'next-intl';

import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import Card from '@/components/ui/molecules/card';
import BugReportModal from '@/components/ui/organisms/modals/bugReportModal';
import IdeaReportModal from '@/components/ui/organisms/modals/ideaReportModal';

const Evolutions = () => {
  const [bugModalOpen, setBugModalOpen] = useState(false);
  const [ideaModalOpen, setIdeaModalOpen] = useState(false);
  const t = useTranslations('components.common.home.evolutions');

  return (
    <section id="evolution" className="px-6 py-[100px]">
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
        <div className="mb-2 grid grid-cols-1 gap-4 md:grid-cols-2">
          <Card variant="error" className="gap-2">
            <span className="text-[2rem]" aria-hidden>
              🐛
            </span>
            <Typography tag="h3" className="font-title text-base font-bold text-white">
              {t('bug.title')}
            </Typography>
            <Typography tag="p" className="text-[0.82rem] leading-[1.65] text-neutral-500">
              {t('bug.body')}
            </Typography>
            <Button
              variant="error"
              glass
              type="button"
              label={t('bug.cta')}
              className="bg-error/30! border-error/50!"
              onClick={() => setBugModalOpen(true)}
            />
          </Card>
          <Card variant="success" className="gap-2">
            <span className="text-[2rem]" aria-hidden>
              💡
            </span>
            <Typography tag="h3" className="font-title text-base font-bold text-white">
              {t('idea.title')}
            </Typography>
            <Typography tag="p" className="text-[0.82rem] leading-[1.65] text-neutral-500">
              {t('idea.body')}
            </Typography>
            <Button
              variant="success"
              glass
              type="button"
              label={t('idea.cta')}
              className="bg-success/30! border-success/50!"
              onClick={() => setIdeaModalOpen(true)}
            />
          </Card>
        </div>
        <Card
          orientation="vertical"
          className="gap-3 mt-1 rounded-xs border border-primary/15 bg-primary/6 p-5 text-center"
        >
          <Typography tag="p" className="mb-3 text-[0.82rem] text-neutral-500">
            {t('community.body')}
          </Typography>
          <Button variant="secondary" glass label={t('community.cta')} className="self-center" />
        </Card>
      </div>
      <BugReportModal open={bugModalOpen} setOpen={setBugModalOpen} />
      <IdeaReportModal open={ideaModalOpen} setOpen={setIdeaModalOpen} />
    </section>
  );
};

export default Evolutions;
