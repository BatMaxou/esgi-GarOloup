'use client';

import { useEffect } from 'react';
import { useTranslations } from 'next-intl';
import { toast } from 'react-toastify';

import LoginForm from '@/components/common/form/auth/login-form';
import Divider from '@/components/ui/atoms/divider';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Typography from '@/components/ui/atoms/typography';
import { useAuth } from '@/contexts/auth-context';
import { paths } from '@/utils/paths';
import { useRouter } from '@/i18n/navigation';
import Button from '@/components/ui/molecules/button';

const LoginClient = () => {
  const { user } = useAuth();
  const router = useRouter();
  const tg = useTranslations();
  const t = useTranslations('components.pages.auth.login');

  useEffect(() => {
    if (user) {
      toast.success(t('loginSuccess'));
      setTimeout(() => {
        router.push(paths.home);
      }, 1000);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
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
        <div className="flex flex-row justify-center items-center gap-2">
          <Typography variant="body-sm" tag="p" bold center textColor="neutral-500">
            {t('noAccount')}
          </Typography>
          <Button asLink variant="text" label={t('createAccount')} href={paths.register} />
        </div>
      </GlassPanel>
    </main>
  );
};

export default LoginClient;
