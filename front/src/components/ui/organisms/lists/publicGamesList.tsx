'use client';

import { useCallback, useMemo, useState } from 'react';
import { useTranslations } from 'next-intl';
import { toast } from 'react-toastify';

import { CollectionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import Button from '@/components/ui/molecules/button';
import DataTable, {
  compareNumbersAsc,
  compareStringsAsc,
  type DataTableColumn,
} from '@/components/ui/organisms/data-table';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { paths } from '@/utils/paths';
import { Game } from '@/utils/types';
import { useRouter } from '@/i18n/navigation';
import ClickAndCopy from '../click-and-copy';

type Props = {
  publicGames: CollectionResponse<Game> | null;
};

const PublicGamesList = ({ publicGames }: Props) => {
  const allGames = useMemo(() => publicGames?.member ?? [], [publicGames]);
  const { apiClient } = useApiClient();
  const router = useRouter();
  const t = useTranslations('components.ui.organisms.lists.publicGamesList');
  const [isLoading, setIsLoading] = useState<string | null>(null);

  const handleJoinGame = useCallback(
    async (joinCode: string) => {
      setIsLoading(joinCode);
      const response = await apiClient.game.join({ joinCode });
      if (!(response instanceof ApiClientError)) {
        toast.success(t('joinGameSuccess'));
        setIsLoading(null);
        router.push(paths.game);
        return;
      }
      if (response.code === 409) {
        toast.error(t('alreadyPlayingJoinCurrentGame'));
        setIsLoading(null);
        router.push(paths.game);
        return;
      }
      toast.error(t('joinGameError'));
      setIsLoading(null);
    },
    [apiClient, router, t]
  );

  const columns = useMemo<DataTableColumn<Game>[]>(
    () => [
      {
        id: 'joinCode',
        header: t('column.joinCode'),
        sortable: true,
        compareAscending: (left, right) => compareStringsAsc(left.joinCode ?? '', right.joinCode ?? ''),
        getFilterText: (game) => game.joinCode ?? '',
        cell: (game) => (
          <ClickAndCopy valueToCopy={game.joinCode ?? ''} iconClassName="text-accent text-glow-accent">
            <Typography tag="span" variant="subtitle" bold textColor="accent" className="text-glow-accent">
              {game.joinCode ?? ''}
            </Typography>
          </ClickAndCopy>
        ),
      },
      {
        id: 'players',
        header: t('column.players'),
        sortable: true,
        compareAscending: (left, right) => {
          const currentLeft = left.players?.length ?? 0;
          const currentRight = right.players?.length ?? 0;
          if (currentLeft !== currentRight) {
            return compareNumbersAsc(currentLeft, currentRight);
          }
          return compareNumbersAsc(left.maxPlayers ?? 0, right.maxPlayers ?? 0);
        },
        getFilterText: (game) => `${game.players?.length ?? 0}/${game.maxPlayers ?? 0}`,
        cell: (game) => (
          <Typography tag="span" variant="body-sm">
            {game.players?.length ?? 0} {t('playersLengthConnector', { count: game.maxPlayers ?? 0 })}
          </Typography>
        ),
      },
      {
        id: 'gameMaster',
        header: t('column.gameMaster'),
        sortable: true,
        thClassName: 'whitespace-nowrap',
        compareAscending: (left, right) => compareNumbersAsc(left.gameMaster ? 1 : 0, right.gameMaster ? 1 : 0),
        getFilterText: (game) => (game.gameMaster ? t('gameMasterYes') : ''),
        cell: (game) => (
          <span className="inline-flex items-center gap-2">
            {game.gameMaster ? (
              <>
                <Icon name="crown" className="size-4 shrink-0 text-accent" />
                <span className="sr-only">{t('gameMasterYes')}</span>
              </>
            ) : (
              <span className="text-neutral-500">—</span>
            )}
          </span>
        ),
      },
      {
        id: 'discussion',
        header: t('column.discussion'),
        sortable: true,
        compareAscending: (left, right) =>
          compareNumbersAsc(left.maxTimeForDiscussion ?? 0, right.maxTimeForDiscussion ?? 0),
        getFilterText: (game) => (game.maxTimeForDiscussion ? String(Math.round(game.maxTimeForDiscussion / 60)) : ''),
        cell: (game) => {
          const minutes = Math.round((game.maxTimeForDiscussion ?? 0) / 60);
          return minutes > 0 ? (
            <span className="inline-flex items-center gap-2">
              <Icon name="timer" className="size-4 shrink-0 text-neutral-300" />
              <Typography tag="span" variant="body-sm">
                {t('discussionMinutes', { minutes })}
              </Typography>
            </span>
          ) : (
            <span className="text-neutral-500">—</span>
          );
        },
      },
      {
        id: 'actions',
        header: t('column.actions'),
        headerAlign: 'center',
        tdClassName: 'text-right',
        cell: (game) => {
          const joinCode = game.joinCode ?? '';
          return (
            <Button
              type="button"
              variant="gradient"
              size="sm"
              label={t('joinButton')}
              disabled={isLoading === joinCode}
              onClick={() => void handleJoinGame(joinCode)}
            />
          );
        },
      },
    ],
    [t, isLoading, handleJoinGame]
  );

  if (allGames.length === 0) {
    return (
      <Typography variant="body-sm" textColor="neutral-500" className="py-8 text-center">
        {t('emptyList')}
      </Typography>
    );
  }

  return (
    <DataTable<Game>
      rows={allGames}
      columns={columns}
      getRowId={(game) => game.id ?? game.joinCode ?? ''}
      defaultSortColumnId="joinCode"
      filter={{
        label: t('filterLabel'),
        placeholder: t('filterPlaceholder'),
      }}
      noMatchMessage={t('noMatchingFilter')}
    />
  );
};

export default PublicGamesList;
