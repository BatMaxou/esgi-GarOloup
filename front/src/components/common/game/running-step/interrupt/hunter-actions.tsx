'use client';

import { useMemo, useState } from 'react';
import { useTranslations } from 'next-intl';

import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';
import { useApiClient } from '@/contexts/api-context';
import Icon from '@/components/ui/atoms/icon';
import { IconName } from '@/components/ui/atoms/icon/config';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import { GameRoleEnum } from '@/utils/enums';
import { ApiClientError } from '@/lib/api/ApiClientError';
import type { Player, SeerRole } from '@/utils/types';

const roleIconMap: Partial<Record<GameRoleEnum, IconName>> = {
  [GameRoleEnum.WEREWOLF]: 'werewolf',
  [GameRoleEnum.WITCH]: 'witch',
  [GameRoleEnum.SEER]: 'seer',
};

const getUsername = (player?: Player) =>
  (player?.user?.username || player?.tempUser?.username || player?.username) ?? '';

const HunterActions = () => {
  const { game } = useGame();
  const { player } = usePlayer();
  const { apiClient } = useApiClient();
  const t = useTranslations('components.common.game.hunterActions');
  const [targetId, setTargetId] = useState<string | null>(null);
  const [shooting, setShooting] = useState(false);

  const myRole = player?.role;
  const observedRoles = useMemo(
    () => (myRole?.type === GameRoleEnum.SEER ? ((myRole as SeerRole).observedRoles ?? {}) : {}),
    [myRole]
  );

  const getKnownRole = (playerId: string): GameRoleEnum | null => {
    if (player && playerId === player.id) {
      return myRole?.type ?? null;
    }
    return observedRoles[playerId] ?? null;
  };

  const alivePlayers = useMemo(
    () => game?.players?.filter((gamePlayer) => !gamePlayer.dead && gamePlayer.id !== player?.id) ?? [],
    [game?.players, player?.id]
  );

  const handleShoot = async () => {
    if (!targetId || shooting) {
      return;
    }
    setShooting(true);
    const response = await apiClient.hunter.shoot(targetId);
    if (response instanceof ApiClientError) {
      setShooting(false);
      return;
    }
    setShooting(false);
  };

  return (
    <div className="flex flex-col gap-8 h-full">
      <div className="flex flex-col gap-2">
        <Typography tag="h1" variant="subtitle" center bold>
          {t('title')}
        </Typography>
        <Typography tag="p" variant="body-sm" textColor="neutral-500" center>
          {t('description')}
        </Typography>
      </div>
      <div className="grid grid-cols-3 auto-rows-min gap-3 w-full flex-1 min-h-0 overflow-y-auto scrollbar">
        {alivePlayers.map((gamePlayer) => {
          const knownRole = getKnownRole(gamePlayer.id);
          const iconName: IconName = (knownRole && roleIconMap[knownRole]) || 'questionMark';
          const isTarget = targetId === gamePlayer.id;

          return (
            <Card
              key={gamePlayer.id}
              onClick={() => setTargetId(gamePlayer.id)}
              isCurrentPlayer={isTarget}
              liftOnHover={false}
              hoverable={!isTarget}
              className={`relative flex flex-col items-center justify-center gap-3 min-h-32 border! cursor-pointer
                ${isTarget ? 'border-error! bg-error/20!' : 'border-primary/15! hover:bg-error/10 hover:border-error/50!'}
              `}
            >
              <Icon name={iconName} className="w-8 h-8 text-primary/70" />
              <Typography variant="body" textColor="light" center>
                {getUsername(gamePlayer)}
              </Typography>
            </Card>
          );
        })}
      </div>
      <Button
        type="button"
        onClick={handleShoot}
        variant="gradient"
        className="w-full"
        disabled={!targetId || shooting}
        loading={shooting}
        label={t('submit')}
      />
    </div>
  );
};

export default HunterActions;
