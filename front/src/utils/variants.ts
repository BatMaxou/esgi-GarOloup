import { GameTeamEnum } from './enums';

export type GameTeamVariantType = 'success' | 'error' | 'primary' | 'accent' | 'secondary';

export const gameTeamVariant = (gameTeam: GameTeamEnum): GameTeamVariantType => {
  switch (gameTeam) {
    case GameTeamEnum.VILLAGE:
      return 'success';
    case GameTeamEnum.WEREWOLF:
      return 'error';
    case GameTeamEnum.SOLO:
      return 'secondary';
    case GameTeamEnum.COUPLE:
      return 'accent';
    default:
      return 'secondary';
  }
};
