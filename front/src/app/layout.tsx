import { ReactNode } from 'react';

import './globals.css';
import { ApiClientProvider } from '@/contexts/api-context';
import { MercureClientProvider } from '@/contexts/mercure-context';

type Props = {
  children: ReactNode;
};

const Providers = ({ children }: Props) => {
  return (
    <>
      <ApiClientProvider>
        <MercureClientProvider>{children}</MercureClientProvider>
      </ApiClientProvider>
    </>
  );
};

const RootLayout = ({ children }: Props) => {
  return (
    <html lang="fr">
      <body className="bg-linear-(--background-gradient) bg-no-repeat min-h-screen scrollbar">
        <Providers>{children}</Providers>
      </body>
    </html>
  );
};

export default RootLayout;
