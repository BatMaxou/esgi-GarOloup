import { ReactNode } from 'react';

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
      <body>
        <Providers>{children}</Providers>
      </body>
    </html>
  );
};

export default RootLayout;
