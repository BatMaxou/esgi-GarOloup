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
import TempUserRegistrationForm from '@/components/common/form/auth/temp-user-registration-form';
import Tabs from '@/components/ui/organisms/tabs';

const LoginClient = () => {
  const { user } = useAuth();
  const router = useRouter();
  const t = useTranslations('components.pages.auth.login');

  useEffect(() => {
    if (user) {
      // Redirection vers la page créer / rejoindre une partie
      // router.push(paths.home);
    }
  }, [user, router]);

  const handleLoginSuccess = () => {
    toast.success(t('loginSuccess'));
    setTimeout(() => {
      router.push(paths.home);
    }, 1000);
  };

  return (
    <main className="py-8 sm:py-16 md:py-24 lg:py-32">
      <Tabs
        tabs={[
          {
            label: t('guest'),
            component: (
              <div className="flex flex-col items-center justify-center gap-4 w-full">
                <Typography variant="heading-3" textColor="accent" bold center special className="text-glow-accent">
                  {t('playAsGuest')}
                </Typography>
                <GlassPanel className="w-[min(100%,500px)] flex flex-col justify-start gap-4 p-8 h-fit">
                  <div className="flex flex-col items-center justify-center gap-4">
                    <Typography variant="subtitle" bold textColor="light" className="mt-4">
                      {t('playAsGuestTitle')}
                    </Typography>
                    <Typography variant="body" textColor="neutral-500" className="mt-4">
                      {t('playAsGuestSubtitle')}
                    </Typography>
                    <TempUserRegistrationForm onSuccess={() => handleLoginSuccess()} />
                  </div>
                </GlassPanel>
              </div>
            ),
          },
          {
            label: t('member'),
            component: (
              <div className="flex flex-col items-center justify-center gap-4 w-full">
                <Typography variant="heading-3" textColor="accent" bold center special className="text-glow-accent">
                  {t('alreadyHaveAccount')}
                </Typography>

                <GlassPanel className="w-[min(100%,500px)] flex flex-col justify-start gap-4 p-8 h-fit">
                  <Typography variant="subtitle" tag="h1" bold center>
                    {t('loginTitle')}
                  </Typography>
                  <Typography variant="body-sm" tag="p" bold center>
                    {t('loginSubtitle')}
                  </Typography>
                  <Divider variant="primary" />
                  <LoginForm onSuccess={() => handleLoginSuccess()} />
                  <div className="flex flex-row justify-center items-center gap-2">
                    <Typography variant="body-sm" tag="p" bold center textColor="neutral-500">
                      {t('loginNoAccount')}
                    </Typography>
                    <Button asLink variant="text" label={t('loginCreateAccount')} href={paths.register} />
                  </div>
                </GlassPanel>
              </div>
            ),
          },
        ]}
        align="center"
      />
    </main>
  );
};

export default LoginClient;
