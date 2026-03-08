import Footer from '@/components/layout/footer';
import Navbar from '@/components/layout/navbar';
import { ReactNode } from 'react';

type Props = {
  children: ReactNode;
};

const WebsiteLayout = ({ children }: Props) => {
  return (
    <div className="bg-linear-(--background-gradient) bg-no-repeat text-foreground grid grid-rows-[auto_1fr_auto] min-h-screen scrollbar">
      <Navbar />
      {children}
      <Footer />
    </div>
  );
};

export default WebsiteLayout;
