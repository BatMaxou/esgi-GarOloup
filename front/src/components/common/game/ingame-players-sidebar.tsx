'use client';

import cn from 'classnames';

import { useState } from 'react';
import { useTranslations } from 'next-intl';

import Typography from '@/components/ui/atoms/typography';
import Divider from '@/components/ui/atoms/divider';
import Icon from '@/components/ui/atoms/icon';
import Card from '@/components/ui/molecules/card';
import PlayerSidebarItem from '@/components/ui/organisms/items/player-sidebar-item';
import { headerSidebarClasses } from '@/components/common/layout/header-surface';
import { Player } from '@/utils/types';

const IngamePlayersSidebar = ({ players }: { players: Player[] }) => {
  const t = useTranslations('components.common.game.ingame-players-sidebar');
  const [isOpen, setIsOpen] = useState(false);
  const playersList = players ?? [];

  return (
    <>
      <button
        type="button"
        aria-label={t('players')}
        className={cn(
          'fixed bottom-4 left-4 z-50 flex cursor-pointer items-center justify-center rounded-full border border-primary/20 p-3 bg-dark/70 md:hidden'
        )}
        onClick={() => setIsOpen((prev) => !prev)}
      >
        <Icon name={isOpen ? 'x' : 'users'} className="h-5 w-5 text-primary" />
      </button>
      {isOpen && (
        <div className="fixed inset-0 z-30 bg-black/50 md:hidden" onClick={() => setIsOpen(false)} aria-hidden="true" />
      )}
      <aside
        className={cn(
          'flex h-full flex-col',
          'max-md:fixed max-md:inset-y-0 max-md:left-0 max-md:z-40 max-md:w-72 max-md:max-w-[80vw] max-md:transition-transform max-md:duration-300',
          isOpen ? 'max-md:translate-x-0' : 'max-md:-translate-x-full',
          'md:w-1/6 md:shrink-0'
        )}
      >
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
              {playersList.map((player) => (
                <li key={player.id}>
                  <PlayerSidebarItem player={player} />
                </li>
              ))}
            </ul>
          </div>
        </Card>
      </aside>
    </>
  );
};

export default IngamePlayersSidebar;
