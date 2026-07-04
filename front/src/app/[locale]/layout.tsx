import { ReactNode } from 'react';
import type { Viewport } from 'next';
import Image from 'next/image';
import { NextIntlClientProvider } from 'next-intl';
import { ToastContainer } from 'react-toastify';

import '@/app/globals.css';
import { ApiClientProvider } from '@/contexts/api-context';
import { MercureClientProvider } from '@/contexts/mercure-context';
import { AuthProvider } from '@/contexts/auth-context';
import { ThemeProvider } from '@/contexts/theme-context';

// Empêche l'auto-dark des navigateurs mobiles (Chrome Android) de délaver
// l'image de fond en gris : on déclare que la page gère déjà le thème sombre.
export const viewport: Viewport = {
  colorScheme: 'dark',
};

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
          <AuthProvider>
            <ApiClientProvider>
              <MercureClientProvider>{children}</MercureClientProvider>
            </ApiClientProvider>
          </AuthProvider>
        </ThemeProvider>
      </NextIntlClientProvider>
    </>
  );
};

const RootLayout = async ({ children }: Props) => {
  return (
    <html lang="fr">
      <body className="bg-linear-(--background-gradient) bg-no-repeat text-foreground min-h-dvh scrollbar transition-colors relative">
        <Image
          src="/images/night-camp-background.png"
          alt="Background"
          className="opacity-90 dark:opacity-60 !fixed inset-0 object-cover object-center -z-1"
          fill
        />
        <ToastContainer toastStyle={{ backgroundColor: 'var(--color-secondary)', color: 'white' }} />
        <Providers>{children}</Providers>
      </body>
    </html>
  );
};

export default RootLayout;
