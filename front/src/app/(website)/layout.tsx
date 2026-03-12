import { ReactNode } from 'react';

import Footer from '@/components/common/layout/footer';
import Navbar from '@/components/common/layout/navbar';

type Props = {
  children: ReactNode;
};

const WebsiteLayout = ({ children }: Props) => {
  return (
    <div className="grid grid-rows-[auto_1fr_auto] min-h-screen">
      <Navbar />
      {children}
      <Footer />
    </div>
  );
};

export default WebsiteLayout;
