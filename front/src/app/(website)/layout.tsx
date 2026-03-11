import { ReactNode } from 'react';

import Footer from '@/components/common/layout/footer';
import Navbar from '@/components/common/layout/navbar';

type Props = {
  children: ReactNode;
};

const WebsiteLayout = ({ children }: Props) => {
  return (
    <div className="bg-linear-(--background-gradient) bg-no-repeat text-foreground grid grid-rows-[auto_1fr_auto] min-h-screen transition-colors">
      <Navbar />
      {children}
      <Footer />
    </div>
  );
};

export default WebsiteLayout;
