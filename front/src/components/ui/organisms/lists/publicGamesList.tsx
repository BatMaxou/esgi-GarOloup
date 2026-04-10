'use client';

import { useState } from 'react';
import { useTranslations } from 'next-intl';
import { CollectionResponse } from '@/lib/api/ApiClient';
import { Game } from '@/utils/types';
import Card from '../../molecules/card';
import Typography from '../../atoms/typography';
import Button from '../../molecules/button';
import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { toast } from 'react-toastify';
import { paths } from '@/utils/paths';
import { useRouter } from '@/i18n/navigation';

type Props = {
  publicGames: CollectionResponse<Game> | null;
};

const PublicGamesList = ({ publicGames }: Props) => {
  const allGames = publicGames?.member ?? [];
  const allGamesLength = publicGames?.totalItems ?? 0;
  const { apiClient } = useApiClient();
  const router = useRouter();
  const t = useTranslations('components.ui.organisms.lists.publicGamesList');
  const [isLoading, setIsLoading] = useState<string | null>(null);

  const handleJoinGame = async (joinCode: string) => {
    setIsLoading(joinCode);
      const response = await apiClient.game.join({ joinCode });
      if (!(response instanceof ApiClientError)) {
        toast.success(t('joinGameSuccess'));
        setIsLoading(null);
        router.push(paths.game);
        return;
      } else {
        if (response.code === 409) {
          toast.error(t('alreadyPlayingJoinCurrentGame'));
          setIsLoading(null);
          router.push(paths.game);
          return;
        }
        toast.error(t('joinGameError'));
      }
      setIsLoading(null);
  };

  return (
    <div className="w-full">
      <ul className="flex flex-col gap-4">
        {allGames.map((game) => {
          const id = game.id ?? '';
          const playersLength = game.players?.length ?? 0;
          const joinCode = game.joinCode ?? '';
          const hasGameMaster = game.gameMaster;
          return (
            <Card key={id} variant="gradient" liftOnHover={false} orientation="horizontal" className='w-full flex flex-row items-center gap-8'>
              <Typography tag="h3" variant="subtitle" bold textColor="accent" className='text-glow-accent'>{joinCode}</Typography>
              <Typography tag="span" variant="body-sm" className='self-end'>{playersLength} {t('playersLengthConnector', { count: playersLength })} </Typography>
              <Button variant="gradient" size="sm" label="Join" disabled={isLoading === joinCode} onClick={() => handleJoinGame(joinCode)}/>
            </Card>
          )
        })}
      </ul>
    </div>
  );
};

export default PublicGamesList;