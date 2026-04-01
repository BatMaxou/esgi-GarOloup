import { ReactNode } from 'react';
import Image from 'next/image';
import { NextIntlClientProvider } from 'next-intl';
import { ToastContainer } from 'react-toastify';

import '@/app/globals.css';
import { ApiClientProvider } from '@/contexts/api-context';
import { MercureClientProvider } from '@/contexts/mercure-context';
import { AuthProvider } from '@/contexts/auth-context';
import { ThemeProvider } from '@/contexts/theme-context';
import { RoleProvider } from '@/contexts/role-context';

type ProvidersProps = {
  children: ReactNode;
};

type Props = {
  children: ReactNode;
};

const Providers = ({ children }: ProvidersProps) => {
  return (
    <>
      <NextIntlClientProvider>
        <ThemeProvider>
          <ToastContainer toastStyle={{ backgroundColor: 'var(--color-secondary)', color: 'white' }} />
          <ApiClientProvider>
            <MercureClientProvider>
              <AuthProvider>
                <RoleProvider>{children}</RoleProvider>
              </AuthProvider>
            </MercureClientProvider>
          </ApiClientProvider>
        </ThemeProvider>
      </NextIntlClientProvider>
    </>
  );
};

const RootLayout = async ({ children }: Props) => {
  return (
    <html lang="fr">
      <body className="bg-linear-(--background-gradient) bg-no-repeat text-foreground min-h-screen scrollbar transition-colors">
        <Image
          src="/images/night-camp-background.png"
          alt="Background"
          className="opacity-90 dark:opacity-60 !fixed inset-0 object-cover object-center -z-1"
          fill
        />
        <Providers>{children}</Providers>
      </body>
    </html>
  );
};

export default RootLayout;
