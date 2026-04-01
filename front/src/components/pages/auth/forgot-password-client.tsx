'use client';

import { useTranslations } from 'next-intl';

import ForgotPasswordForm from '@/components/common/form/auth/forgot-password-form';
import Divider from '@/components/ui/atoms/divider';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Typography from '@/components/ui/atoms/typography';

const ForgotPasswordClient = () => {
  const tg = useTranslations();
  const t = useTranslations('components.pages.auth.forgotPassword');

  return (
    <main className="flex justify-center px-2 py-8 xs:px-8 sm:px-16 sm:py-16 md:px-32 md:py-32">
      <GlassPanel className="w-[min(100%,500px)] flex flex-col justify-start gap-4 p-8 h-fit">
        <Typography variant="heading-3" textColor="accent" bold center special className="text-glow-accent">
          {tg('name')}
        </Typography>
        <Typography variant="subtitle" tag="h1" bold center>
          {t('title')}
        </Typography>
        <Typography variant="body-sm" tag="p" bold center>
          {t('subtitle')}
        </Typography>
        <Divider variant="primary" />
        <ForgotPasswordForm />
      </GlassPanel>
    </main>
  );
};

export default ForgotPasswordClient;
