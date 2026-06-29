import Icon from "@/components/ui/atoms/icon";
import Typography from "@/components/ui/atoms/typography";
import { useGame } from "@/contexts/game-context";
import { GameRoleEnum } from "@/utils/enums";
import { useTranslations } from "next-intl";

const WaitingNightActions = () => {
  const t = useTranslations('components.common.game.waitingNightActions');
  const { game } = useGame();
  const currentRole = Object.keys(game?.nightWorkflow?.currentTurn ?? {})[0];

  const renderCurrentTurnRoleIndicator = () => {
    switch(currentRole) {
      case GameRoleEnum.WEREWOLF:
        return (
        <Typography variant="subtitle" className="animate-pulse">
            {t('werewolfTurn')}
          </Typography>);
      case GameRoleEnum.WITCH:
        return (<Typography variant="subtitle" className="animate-pulse">
            {t('witchTurn')}
          </Typography>);
      case GameRoleEnum.SEER:
        return (<Typography variant="subtitle" className="animate-pulse">
            {t('seerTurn')}
        </Typography>);
      case GameRoleEnum.HUNTER:
        return (<Typography variant="subtitle" className="animate-pulse">
            {t('hunterTurn')}
        </Typography>);
      case GameRoleEnum.WILD_CHILD:
        return (<Typography variant="subtitle" className="animate-pulse">
            {t('wildChildTurn')}
        </Typography>);
      case GameRoleEnum.INFECT_FATHER:
        return (<Typography variant="subtitle" className="animate-pulse">
            {t('infectFatherTurn')}
        </Typography>);
    }
  }
  return (
    <div className="flex flex-col items-center justify-center gap-8 h-full w-full">
      <Icon name="garoloup" className="animate-pulse w-20 h-20" />
      <div className="flex flex-col items-center justify-center gap-2">
        {renderCurrentTurnRoleIndicator()}
      </div>
    </div>
  );
};

export default WaitingNightActions;