'use client';

import { useMemo, useState } from 'react';
import { useTranslations } from 'next-intl';

import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';
import Icon from '@/components/ui/atoms/icon';
import { IconName } from '@/components/ui/atoms/icon/config';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import { GameRoleEnum } from '@/utils/enums';
import type { Ballot, Player, SeerRole } from '@/utils/types';
import Tooltip from '@/components/ui/atoms/tooltip';

const roleIconMap: Partial<Record<GameRoleEnum, IconName>> = {
  [GameRoleEnum.WEREWOLF]: 'werewolf',
  [GameRoleEnum.WITCH]: 'witch',
  [GameRoleEnum.SEER]: 'seer',
};

const getUsername = (player?: Player) =>
  (player?.user?.username || player?.tempUser?.username || player?.username) ?? '';

const VoteDisplay = () => {
  const { game, vote } = useGame();
  const { player } = usePlayer();
  const t = useTranslations('components.common.game.vote');
  const [votingId, setVotingId] = useState<string | null>(null);

  const myRole = player?.role;
  const observedRoles = useMemo(
    () => (myRole?.type === GameRoleEnum.SEER ? ((myRole as SeerRole).observedRoles ?? {}) : {}),
    [myRole]
  );

  const getKnownRole = (targetId: string): GameRoleEnum | null => {
    if (player && targetId === player.id) {
      return myRole?.type ?? null;
    }
    return observedRoles[targetId] ?? null;
  };

  const currentVote = useMemo(() => game?.votes?.find((vote) => !vote.resolved), [game?.votes]);

  const votersByTarget = useMemo(
    () =>
      currentVote?.ballots?.reduce<Record<string, Ballot[]>>((acc, ballot) => {
        const targetId = ballot.target?.id;
        if (targetId) {
          (acc[targetId] ??= []).push(ballot);
        }
        return acc;
      }, {}) ?? {},
    [currentVote?.ballots]
  );

  const myTargetId = useMemo(
    () => currentVote?.ballots?.find((ballot) => ballot.player?.id === player?.id)?.target?.id ?? null,
    [currentVote?.ballots, player?.id]
  );

  const votablePlayers = useMemo(
    () => game?.players?.filter((gamePlayer) => !gamePlayer.dead && gamePlayer.id !== player?.id) ?? [],
    [game?.players, player?.id]
  );

  const handleVote = async (targetId: string) => {
    if (votingId) {
      return;
    }
    setVotingId(targetId);
    await vote(targetId);
    setVotingId(null);
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
        {votablePlayers.map((gamePlayer) => {
          const knownRole = getKnownRole(gamePlayer.id);
          const iconName: IconName = (knownRole && roleIconMap[knownRole]) || 'questionMark';
          const voters = votersByTarget[gamePlayer.id] ?? [];
          const isMyTarget = myTargetId === gamePlayer.id;

          return (
            <Card
              key={gamePlayer.id}
              onClick={() => handleVote(gamePlayer.id)}
              isCurrentPlayer={isMyTarget}
              liftOnHover={false}
              hoverable={!isMyTarget}
              className={`group relative flex flex-col items-center justify-center gap-3 min-h-32 border! cursor-pointer
                ${
                  isMyTarget
                    ? 'border-error! bg-error/20!'
                    : 'border-primary/15! hover:bg-primary/10 hover:border-primary/50!'
                }
                ${votingId === gamePlayer.id ? 'opacity-60 pointer-events-none' : ''}
              `}
            >
              {voters.length > 0 && (
                <div className="absolute top-2 right-2 flex flex-row -space-x-3 group-hover:-space-x-1">
                  {(voters.length > 2 ? voters.slice(0, 2) : voters).map((ballot) => (
                    <Tooltip
                      key={ballot.player?.id ?? getUsername(ballot.player)}
                      content={getUsername(ballot.player)}
                      className="transition-[margin] duration-300 ease-out"
                    >
                      <span className="flex items-center justify-center w-7 h-7 rounded-full bg-primary/30 border border-primary/50 text-xs font-bold text-primary uppercase shadow-sm">
                        {getUsername(ballot.player).charAt(0)}
                      </span>
                    </Tooltip>
                  ))}
                  {voters.length > 2 && (
                    <Tooltip
                      className="transition-[margin] duration-300 ease-out"
                      content={
                        voters.length - 2 === 1 ? (
                          getUsername(voters[2].player)
                        ) : (
                          <ul className="list-disc list-inside text-left">
                            {voters.slice(2).map((ballot) => (
                              <li key={ballot.player?.id ?? getUsername(ballot.player)}>
                                {getUsername(ballot.player)}
                              </li>
                            ))}
                          </ul>
                        )
                      }
                    >
                      <span className="flex items-center justify-center w-7 h-7 rounded-full bg-primary/30 border border-primary/50 text-xs font-bold text-primary shadow-sm">
                        +{voters.length - 2}
                      </span>
                    </Tooltip>
                  )}
                </div>
              )}
              <Icon name={iconName} className="w-8 h-8 text-primary/70" />
              <Typography variant="body" textColor="light" center>
                {getUsername(gamePlayer)}
              </Typography>
            </Card>
          );
        })}
      </div>
    </div>
  );
};

export default VoteDisplay;
