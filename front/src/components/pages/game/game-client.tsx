'use client';

import IngamePlayersSidebar from '@/components/common/game/ingame-players-sidebar';
import MyStatusBadges from '@/components/common/game/my-role/my-status-badges';
import RunningGameDisplay from '@/components/common/game/running-game-display';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';
import { AssassinProvider } from '@/contexts/roles/assassin-context';
import { CupidonProvider } from '@/contexts/roles/cupidon-context';
import { InfectFatherProvider } from '@/contexts/roles/infect-father-context';
import { isLoverRole, LoverProvider } from '@/contexts/roles/lover-context';
import { SeerProvider } from '@/contexts/roles/seer-context';
import { WereWolfProvider } from '@/contexts/roles/werewolf-context';
import { WildChildProvider } from '@/contexts/roles/wild-child-context';
import { WitchProvider } from '@/contexts/roles/witch-context';
import { GameGlobalStepEnum, GameRoleEnum, GameRuntimeStepEnum, GameTeamEnum } from '@/utils/enums';
import type { Player } from '@/utils/types';
import { useTranslations } from 'next-intl';
import { useMemo, useEffect } from 'react';
import { useRouter } from '@/i18n/navigation';
import { paths } from '@/utils/paths';

const GameClient = () => {
  const { game } = useGame();
  const { player } = usePlayer();
  const t = useTranslations('components.pages.game.gameClient');
  const router = useRouter();

  useEffect(() => {
    if (game?.globalStep === GameGlobalStepEnum.FINISH && game?.id) {
      router.push({ pathname: paths.recap, params: { gameId: game.id } });
    }
  }, [game?.globalStep, game?.id, router]);

  const isPlayerSpectator = game?.players?.some(
    (currentPlayer) => currentPlayer.id === player?.id && currentPlayer.dead
  );

  const isInterruptTurnForPlayer =
    game?.runtimeStep === GameRuntimeStepEnum.INTERRUPT && game?.interruptedByRole === player?.role?.type;

  const showSpectatorOverlay =
    isPlayerSpectator && !isInterruptTurnForPlayer && game?.globalStep === GameGlobalStepEnum.RUNNING;

  const playersList = useMemo(() => {
    if (!game) {
      return [];
    }

    const normalizedPlayers: Player[] = Array.isArray(game.players)
      ? game.players
      : (Object.values(game.players ?? {}) as Player[]);

    const gameMasterUsername = game.gameMaster?.user?.username ?? game.gameMaster?.username;
    const normalizedGameMaster = game.gameMaster
      ? {
          id: game.gameMaster.id,
          user: gameMasterUsername
            ? {
                id: game.gameMaster.id,
                username: gameMasterUsername,
              }
            : undefined,
        }
      : null;

    if (!normalizedGameMaster) {
      return normalizedPlayers;
    }

    const playersWithoutGameMaster = normalizedPlayers.filter(
      (currentPlayer) => currentPlayer.id !== normalizedGameMaster.id
    );
    return [normalizedGameMaster, ...playersWithoutGameMaster];
  }, [game]);

  const isHost = player?.id === game?.host?.id;
  const isGameMaster = player?.id === game?.gameMaster?.id;

  const renderGameDisplay = useMemo(() => {
    let content = (
      <>
        <IngamePlayersSidebar players={playersList || []} />
        <div className="px-8 py-8 w-full h-full">
          <RunningGameDisplay isHost={isHost} isGameMaster={isGameMaster} />
          {showSpectatorOverlay && (
            <>
              <div className="absolute inset-0 z-10 bg-[radial-gradient(ellipse_at_center,transparent_40%,rgba(0,0,0,0.6)_100%)]" />
              {/* Render une icon d'oeil et un texte "Spectateur en absolute en haut à droite" */}
              <div className="absolute top-8 right-4 z-20 flex items-center gap-2 rounded-full border border-error/40 bg-error/10 px-3 py-1">
                <Icon name="targetEye" className="w-4 h-4 text-error" />
                <Typography tag="span" variant="subtitle" className="text-error!" bold>
                  {t('spectator')}
                </Typography>
              </div>
            </>
          )}
        </div>
        <MyStatusBadges />
      </>
    );

    switch (player?.role?.type) {
      case GameRoleEnum.INFECT_FATHER:
        content = <InfectFatherProvider>{content}</InfectFatherProvider>;
        break;
      case GameRoleEnum.WITCH:
        content = <WitchProvider>{content}</WitchProvider>;
        break;
      case GameRoleEnum.SEER:
        content = <SeerProvider>{content}</SeerProvider>;
        break;
      case GameRoleEnum.WILD_CHILD:
        content = <WildChildProvider>{content}</WildChildProvider>;
        break;
      case GameRoleEnum.CUPIDON:
        content = <CupidonProvider>{content}</CupidonProvider>;
        break;
      case GameRoleEnum.ASSASSIN:
        content = <AssassinProvider>{content}</AssassinProvider>;
        break;
    }

    if (isLoverRole(player?.role)) {
      content = <LoverProvider>{content}</LoverProvider>;
    }

    return content;
  }, [isGameMaster, isHost, player?.role, playersList, showSpectatorOverlay, t]);

  if (!game || !player) {
    return <>Loading...</>;
  }

  const content = <main className="flex h-full w-full flex-row justify-between items-start">{renderGameDisplay}</main>;

  if (player.team === GameTeamEnum.WEREWOLF) {
    return <WereWolfProvider>{content}</WereWolfProvider>;
  }

  return content;
};

export default GameClient;
