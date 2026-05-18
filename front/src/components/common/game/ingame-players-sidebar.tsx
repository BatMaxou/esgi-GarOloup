import cn from 'classnames';

import Card from '@/components/ui/molecules/card';
import { headerSidebarClasses } from '@/components/common/layout/header-surface';
import { useGame } from '@/contexts/game-context';
import Typography from '@/components/ui/atoms/typography';
import { useTranslations } from 'next-intl';
import Divider from '@/components/ui/atoms/divider';
import PlayerSidebarItem from '@/components/ui/organisms/items/player-sidebar-item';

const IngamePlayersSidebar = () => {
  const { game } = useGame();
  const t = useTranslations('components.common.game.ingame-players-sidebar');
  const players = game?.players ?? [];

  return (
    <aside className="flex h-full min-h-0 w-1/6 shrink-0 flex-col">
      <Card
        variant="likeHeader"
        liftOnHover={false}
        hoverable={false}
        fullfilled
        className={cn('h-full min-h-0', headerSidebarClasses)}
      >
        <div className="flex flex-col gap-4 p-4">
          <div className="flex flex-row justify-between items-center">
            <Typography tag="h2" variant="button" textColor="primary" bold uppercase>
              {t('players')}
            </Typography>
          </div>
          <Divider variant="secondary" className="w-full" orientation="horizontal" />
          <ul className="flex flex-col gap-2">
            {players.map((player) => (
              <li key={player.id}>
                <PlayerSidebarItem player={player} />
              </li>
            ))}
          </ul>
        </div>
      </Card>
    </aside>
  );
};

export default IngamePlayersSidebar;
