'use client';

import { useEffect } from 'react';
import { useTranslations } from 'next-intl';

import LoginForm from '@/components/common/form/auth/login-form';
import Divider from '@/components/ui/atoms/divider';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Typography from '@/components/ui/atoms/typography';
import { useAuth } from '@/contexts/auth-context';
import { paths } from '@/utils/paths';
import { useRouter } from '@/i18n/navigation';

const LoginClient = () => {
  const { user } = useAuth();
  const router = useRouter();
  const tg = useTranslations();
  const t = useTranslations('components.pages.auth.login');

  useEffect(() => {
    if (user) {
      router.push(paths.home);
    }
  }, [user, router]);

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
        {!user && <LoginForm />}
      </GlassPanel>
    </main>
  );
};

export default LoginClient;
