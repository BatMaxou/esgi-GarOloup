'use client';

import { useEffect, useMemo, useState } from 'react';
import { useLocale, useTranslations } from 'next-intl';
import TextSkeleton from '@/components/ui/atoms/skeleton';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import { useRole } from '@/contexts/role-context';
import Tag from '@/components/ui/molecules/tag';
import Divider from '@/components/ui/atoms/divider';
import Button from '@/components/ui/molecules/button';
import { getImagePath } from '@/utils/getImagePath';
import { GameRoleEnum, GameTeamEnum } from '@/utils/enums';
import { Role } from '@/utils/types';
import { TagFilter } from '@/components/ui/molecules/filters';
import { gameTeamVariant } from '@/utils/variants';
import { roleTypeToSlug, type RoleSlugLocale } from '@/utils/roleSlug';
import { Link } from '@/i18n/navigation';
import Image from 'next/image';

const RolesClient = () => {
  const troles = useTranslations('roles');
  const t = useTranslations('components.pages.roles');
  const locale = useLocale() as RoleSlugLocale;
  const {
    roleList,
    roleListLoading,
    filteredRoleList,
    setFilteredRoleList,
    getAllRoles,
    getAllGameTeamFilters,
    gameTeamFilters,
    gameTeamFiltersLoading,
  } = useRole();
  const [selectedGameTeamFilter, setSelectedGameTeamFilter] = useState<GameTeamEnum | 'all'>('all');
  const roleListDynamic = useMemo(() => {
    return selectedGameTeamFilter !== 'all' ? filteredRoleList : roleList;
  }, [filteredRoleList, roleList, selectedGameTeamFilter]);

  useEffect(() => {
    getAllRoles();
    getAllGameTeamFilters();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {}, [roleListDynamic]);

  useEffect(() => {}, [filteredRoleList]);

  const handleUpdateContext = (value: GameTeamEnum | 'all') => {
    if (value === 'all') return;
    const roleListFiltered = roleList.filter((role: Role) => role.teams?.includes(value));
    setFilteredRoleList(roleListFiltered);
  };

  return (
    <main className="px-6 py-[100px] mx-auto max-w-[1100px] w-full">
      <div className="mb-8">
        <Typography tag="div" textColor="primary" bold>
          {t('eyebrow')}
        </Typography>
        <Typography tag="h1" variant="heading-2" bold>
          {t('title')}
        </Typography>
        <Typography tag="p" className="mb-10">
          {t('subtitle')}
        </Typography>
      </div>
      <div className="mb-4 flex flex-row items-center justify-start gap-2">
        <Typography tag="h2" variant="body-sm" bold>
          {t('camp')}
        </Typography>{' '}
        -
        <TagFilter
          labels={gameTeamFilters}
          traductionPath="roles"
          isLoading={gameTeamFiltersLoading}
          onSelectedChange={(value) => {
            setSelectedGameTeamFilter(value);
            handleUpdateContext(value);
          }}
          tagVariant={(value) => gameTeamVariant(value)}
        />
      </div>
      {roleListLoading ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {Array.from({ length: 12 }).map((_, index) => (
            <Card key={index} fullfilled>
              <div className="p-4 flex flex-col items-start justify-between bg-cover bg-center aspect-square">
                <TextSkeleton variant="body" className="max-w-12!" />
                <TextSkeleton variant="body" className="max-w-24!" />
              </div>
              <Divider variant="secondary" className="w-full" />
              <div className="flex items-center justify-between px-4 py-2">
                <div className="flex items-center justify-start gap-2 w-full">
                  <TextSkeleton variant="body" className="max-w-full!" />
                </div>
                <div className="flex items-center justify-end w-full">
                  <TextSkeleton variant="body" className="max-w-3/5!" />
                </div>
              </div>
            </Card>
          ))}
        </div>
      ) : (
        <>
          {roleListDynamic.length === 0 ? (
            <Card className="w-full" liftOnHover={false} fullfilled>
              <Typography tag="p" variant="button" className="text-center py-34">
                {t('noRolesFoundForThisCamp')}
              </Typography>
            </Card>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <>
                {roleListDynamic.map((role) => (
                  <Card
                    key={role.id}
                    className="isolate overflow-hidden"
                    type="role"
                    variant={role.teams?.[0] || 'village'}
                    fullfilled
                  >
                    <div className="pb-4 flex flex-col items-start justify-between aspect-square rounded-t-sm relative">
                      {role.picture && (
                        <Image
                          src={getImagePath(role.picture)}
                          alt={role.name || ''}
                          fill
                          unoptimized
                          className="-z-1"
                        />
                      )}
                      <div className="p-4 pb-6 bg-linear-to-b from-black/50 to-transparent w-full rounded-sm">
                        <Tag
                          label={role.teams && role.teams.length > 0 ? troles(`${role.teams[0].toLowerCase()}`) : ''}
                          variant={role.teams && role.teams.length > 0 ? gameTeamVariant(role.teams[0]) : null}
                        />
                      </div>
                      <div className="px-4">
                        <Typography variant="subtitle" bold className="text-glow-dark">
                          {role.name}
                        </Typography>
                      </div>
                    </div>
                    <Divider variant="secondary" className="w-full" />
                    <div className="flex items-center justify-between px-4 py-2">
                      <div className="flex items-center justify-start gap-2">
                        {role.minPlayers && (
                          <Typography tag="p" variant="body-xs">
                            min <b>{role.minPlayers}</b>
                          </Typography>
                        )}
                        {role.maxPerGame && (
                          <Typography tag="p" variant="body-xs">
                            max <b>{role.maxPerGame}</b>/{t('game')}
                          </Typography>
                        )}
                      </div>
                      <div className="flex items-center justify-end">
                        {role.type && (
                          <Link
                            href={{
                              pathname: '/roles/[slug]',
                              params: { slug: roleTypeToSlug(role.type as GameRoleEnum, locale) },
                            }}
                          >
                            <Button variant="text" size="sm" label={t('seeRole')} />
                          </Link>
                        )}
                      </div>
                    </div>
                  </Card>
                ))}
              </>
            </div>
          )}
        </>
      )}
    </main>
  );
};

export default RolesClient;
