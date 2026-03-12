import { ReactNode } from 'react';
import Image from 'next/image';

import './globals.css';
import { ApiClientProvider } from '@/contexts/api-context';
import { MercureClientProvider } from '@/contexts/mercure-context';
import { AuthProvider } from '@/contexts/auth-context';
import { ThemeProvider } from '@/contexts/theme-context';

type Props = {
  children: ReactNode;
};

const Providers = ({ children }: Props) => {
  return (
    <>
      <ThemeProvider>
        <ApiClientProvider>
          <MercureClientProvider>
            <AuthProvider>{children}</AuthProvider>
          </MercureClientProvider>
        </ApiClientProvider>
      </ThemeProvider>
    </>
  );
};

const RootLayout = ({ children }: Props) => {
  return (
    <html lang="fr">
      <body className="bg-linear-(--background-gradient) bg-no-repeat text-foreground min-h-screen scrollbar transition-colors">
        <Image
          src="/images/night-camp-background.png"
          alt="Hero background"
          className="opacity-90 dark:opacity-60 !fixed inset-0 object-cover object-center -z-1"
          fill
        />
        <Providers>{children}</Providers>
      </body>
    </html>
  );
};

export default RootLayout;
