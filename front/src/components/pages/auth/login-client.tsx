'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';

import LoginForm from '@/components/common/form/auth/login-form';
import Divider from '@/components/ui/atoms/divider';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Typography from '@/components/ui/atoms/typography';
import { useAuth } from '@/contexts/auth-context';

const LoginClient = () => {
  const { user } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (user) {
      // Get searchParam with key ?redirect= ?
      router.push('/');
    }
  }, [user, router]);

  return (
    <main className="flex justify-center p-16">
      <GlassPanel className="w-[min(100%,500px)] flex flex-col justify-start gap-4 p-8 h-fit">
        <Typography variant="heading-3" textColor="accent" bold center special className="text-glow-accent">
          GarOloup
        </Typography>
        <Typography variant="subtitle" tag="h1" bold center>
          Bon retour parmi nous (todo)
        </Typography>
        <Typography variant="body-sm" tag="p" bold center>
          Connectez-vous pour reprendre la chasse
        </Typography>
        <Divider variant="primary" />
        {!user && <LoginForm />}
      </GlassPanel>
    </main>
  );
};

export default LoginClient;
