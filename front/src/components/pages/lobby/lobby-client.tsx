'use client';

import JoinGame from '@/components/common/lobby/joinGame';
import PublicGames from '@/components/common/lobby/publicGames';
import CreateGame from '@/components/common/lobby/createGame';
import Tabs from '@/components/ui/organisms/tabs';
import { useTranslations } from 'next-intl';

const LobbyClient = () => {
  const t = useTranslations('components.pages.lobby');
  return (
    <main className="flex flex-row justify-center px-2 py-8 xs:px-8 sm:px-16 sm:py-16 md:px-32 md:py-32">
      <Tabs
        tabs={[
          { label: t('public'), component: <PublicGames /> },
          { label: t('join'), component: <JoinGame /> },
          { label: t('create'), component: <CreateGame /> },
        ]}
        align="center"
      />
    </main>
  );
};

export default LobbyClient;
