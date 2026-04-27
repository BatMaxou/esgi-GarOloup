import { ReactNode } from 'react';

import Footer from '@/components/common/layout/footer';
import Navbar from '@/components/common/layout/navbar';
import { PublicGamesProvider } from '@/contexts/public-games-context';

type Props = {
  children: ReactNode;
};

const WebsiteLayout = ({ children }: Props) => {
  return (
    <div className="grid grid-rows-[auto_1fr_auto] min-h-screen">
      <Navbar />
      <PublicGamesProvider>{children}</PublicGamesProvider>
      <Footer />
    </div>
  );
};

export default WebsiteLayout;
