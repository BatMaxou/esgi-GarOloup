'use client';

import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import PublicGamesList from '@/components/ui/organisms/lists/publicGamesList';
import { useGame } from '@/contexts/game-context';
import { Loader } from 'lucide-react';
import { useTranslations } from 'next-intl';
import { useEffect } from 'react';

const PublicGames = () => {
  const { publicGames, getPublicGames, publicGamesLoading } = useGame();
  const t = useTranslations('components.common.lobby.publicGames');
  useEffect(() => {
    getPublicGames(1, 8);
  }, []);

  return (
    <Card className="p-10 w-full" orientation="vertical" hoverable={false}>
    <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span" className='mb-4'>{ t('title') }</Typography>
      {publicGamesLoading
        ?
          <div className='flex justify-center items-center h-full my-20 mx-38'>
            <Loader className='animate-spin' size={48} />
          </div>
        :
        <PublicGamesList publicGames={publicGames} />
      }
    </Card>
  );
};

export default PublicGames;
