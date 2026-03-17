import { ReactNode } from 'react';
import Image from 'next/image';

import './globals.css';
import { ApiClientProvider } from '@/contexts/api-context';
import { MercureClientProvider } from '@/contexts/mercure-context';
import { AuthProvider } from '@/contexts/auth-context';
import { ThemeProvider } from '@/contexts/theme-context';
import { getApiClient } from '@/utils/server/clients';
import { User } from '@/utils/types';
import { ApiClientError } from '@/lib/api/ApiClientError';

type ProvidersProps = {
  user: User | null;
  children: ReactNode;
};

type Props = {
  children: ReactNode;
};

const Providers = ({ user, children }: ProvidersProps) => {
  return (
    <>
      <ThemeProvider>
        <ApiClientProvider>
          <MercureClientProvider>
            <AuthProvider initialUser={user}>{children}</AuthProvider>
          </MercureClientProvider>
        </ApiClientProvider>
      </ThemeProvider>
    </>
  );
};

const RootLayout = async ({ children }: Props) => {
  const apiClient = await getApiClient();

  const maybeUser = await apiClient.me
    .get()
    .then((maybeError) => (maybeError instanceof ApiClientError ? null : maybeError));

  return (
    <html lang="fr">
      <body className="bg-linear-(--background-gradient) bg-no-repeat text-foreground min-h-screen scrollbar transition-colors">
        <Image
          src="/images/night-camp-background.png"
          alt="Background"
          className="opacity-90 dark:opacity-60 !fixed inset-0 object-cover object-center -z-1"
          fill
        />
        <Providers user={maybeUser}>{children}</Providers>
      </body>
    </html>
  );
};

export default RootLayout;
