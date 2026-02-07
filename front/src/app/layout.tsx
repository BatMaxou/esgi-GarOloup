import { ReactNode } from 'react';

import './globals.css';

type Props = {
  children: ReactNode;
};

const RootLayout = ({ children }: Props) => {
  return <html lang="fr">
    <body className="bg-linear-(--background-gradient) bg-no-repeat min-h-screen">
      {children}
    </body>
  </html>
}

export default RootLayout;
