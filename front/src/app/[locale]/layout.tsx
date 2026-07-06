import { ReactNode } from 'react';
import type { Metadata, Viewport } from 'next';
import Image from 'next/image';
import { NextIntlClientProvider } from 'next-intl';
import { ToastContainer } from 'react-toastify';

import '@/app/globals.css';
import { ApiClientProvider } from '@/contexts/api-context';
import { MercureClientProvider } from '@/contexts/mercure-context';
import { AuthProvider } from '@/contexts/auth-context';
import { ThemeProvider } from '@/contexts/theme-context';
import ServiceWorkerRegistration from '@/components/common/layout/service-worker-registration';

export const metadata: Metadata = {
  title: 'GarOloup',
  description: 'Jeu de Loup-Garou en temps réel',
  manifest: '/manifest.json',
  appleWebApp: {
    capable: true,
    statusBarStyle: 'black-translucent',
    title: 'GarOloup',
  },
  formatDetection: { telephone: false },
  icons: {
    icon: [
      { url: '/favicon.ico', sizes: '32x32', type: 'image/x-icon' },
      { url: '/icon.svg', type: 'image/svg+xml' },
    ],
    apple: [{ url: '/apple-touch-icon.png', sizes: '180x180' }],
  },
};

// Empêche l'auto-dark des navigateurs mobiles (Chrome Android) de délaver
// l'image de fond en gris : on déclare que la page gère déjà le thème sombre.
export const viewport: Viewport = {
  colorScheme: 'dark',
  width: 'device-width',
  initialScale: 1,
  viewportFit: 'cover',
  themeColor: '#9f9ad6',
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
        <ServiceWorkerRegistration />
        <Providers>{children}</Providers>
      </body>
    </html>
  );
};

export default RootLayout;
