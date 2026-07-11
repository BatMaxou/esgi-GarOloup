'use client';

import { useContext, useEffect, useState } from 'react';
import { useLocale, useTranslations } from 'next-intl';
import cn from 'classnames';

import Icon from '@/components/ui/atoms/icon';
import type { IconName } from '@/components/ui/atoms/icon/config';
import Typography from '@/components/ui/atoms/typography';
import { usePlayer } from '@/contexts/player-context';
import { useRole } from '@/contexts/role-context';
import { LoverContext } from '@/contexts/roles/lover-context';
import { roleIcon } from '@/components/pages/recap/config';
import { roleTypeToSlug, type RoleSlugLocale } from '@/utils/roleSlug';

import MyRoleDialog from './my-role-dialog';

const pillClasses =
  'group flex items-center rounded-full border p-2.5 bg-dark/80 backdrop-blur-sm cursor-pointer transition-colors';

const revealClasses = (expanded: boolean) =>
  cn(
    'overflow-hidden truncate transition-all duration-300 ease-out',
    'group-hover:max-w-64 group-hover:pl-2.5 group-hover:opacity-100',
    'group-focus-visible:max-w-64 group-focus-visible:pl-2.5 group-focus-visible:opacity-100',
    expanded ? 'max-w-64 pl-2.5 opacity-100' : 'max-w-0 pl-0 opacity-0'
  );

type PillProps = {
  icon: IconName;
  colorClass: string;
  label: string;
  ariaLabel: string;
  onClick: () => void;
  expandable?: boolean;
  expanded?: boolean;
};

const ExpandablePill = ({
  icon,
  colorClass,
  label,
  ariaLabel,
  onClick,
  expandable = true,
  expanded = false,
}: PillProps) => (
  <button type="button" aria-label={ariaLabel} onClick={onClick} className={cn(pillClasses, colorClass)}>
    <Icon name={icon} className="h-5 w-5 shrink-0" />
    <Typography
      tag="span"
      variant="body-sm"
      textColor="controlled"
      bold
      className={expandable ? revealClasses(expanded) : 'truncate max-w-64 pl-2.5'}
    >
      {label}
    </Typography>
  </button>
);

const MyStatusBadges = () => {
  const { player } = usePlayer();
  const { role, roleLoading, getRole } = useRole();
  const lover = useContext(LoverContext);
  const t = useTranslations('components.common.game.myRole');
  const locale = useLocale() as RoleSlugLocale;
  const [open, setOpen] = useState(false);
  const [expandedKey, setExpandedKey] = useState<'couple' | 'infect' | null>(null);

  const roleType = player?.role?.type;

  useEffect(() => {
    if (roleType) {
      getRole({ ref: roleTypeToSlug(roleType, locale) });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [roleType, locale]);

  if (!roleType) {
    return null;
  }

  const team = player?.team;
  const iconName = roleIcon[roleType] ?? 'questionMark';

  const partner = lover?.partner;
  const partnerName = partner?.user?.username ?? partner?.tempUser?.username ?? partner?.username;
  const isInfected = Boolean(player?.role?.infected);

  const toggle = (key: 'couple' | 'infect') => setExpandedKey((current) => (current === key ? null : key));

  return (
    <>
      <div className="fixed bottom-4 right-4 z-30 flex flex-col items-end gap-2">
        {lover?.isLover && partnerName && (
          <ExpandablePill
            icon="heart"
            colorClass="border-pink-500/40 text-pink-400"
            label={t('linkedTo', { name: partnerName })}
            ariaLabel={t('linkedTo', { name: partnerName })}
            expanded={expandedKey === 'couple'}
            onClick={() => toggle('couple')}
          />
        )}

        {isInfected && (
          <ExpandablePill
            icon="werewolf"
            colorClass="border-red-500/40 text-red-500"
            label={t('infected')}
            ariaLabel={t('infected')}
            expanded={expandedKey === 'infect'}
            onClick={() => toggle('infect')}
          />
        )}

        <ExpandablePill
          icon={iconName}
          colorClass="border-primary/20 text-primary hover:border-primary/40"
          label={`${t('youAre')} ${role?.name ?? (roleLoading ? t('loading') : '')}`}
          ariaLabel={t('label')}
          expandable={false}
          onClick={() => setOpen(true)}
        />
      </div>

      <MyRoleDialog open={open} setOpen={setOpen} role={role} loading={roleLoading} team={team} />
    </>
  );
};

export default MyStatusBadges;
