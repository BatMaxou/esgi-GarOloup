'use client';

import { useEffect, useMemo } from 'react';
import Image from 'next/image';
import { useLocale, useTranslations } from 'next-intl';

import { Link } from '@/i18n/navigation';
import Divider from '@/components/ui/atoms/divider';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import TextSkeleton from '@/components/ui/atoms/skeleton';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Tag from '@/components/ui/molecules/tag';
import { useRole } from '@/contexts/role-context';
import { getImagePath } from '@/utils/getImagePath';
import { GameTeamEnum } from '@/utils/enums';
import type { Role } from '@/utils/types';
import type { RoleCardVariant } from '@/components/ui/molecules/card';
import { paths } from '@/utils/paths';
import { roleTypeToSlug, type RoleSlugLocale } from '@/utils/roleSlug';
import { gameTeamVariant } from '@/utils/variants';
import { ArrowLeftIcon, ArrowRightIcon } from 'lucide-react';

type Props = {
  roleRef: string;
};

function roleListHref(role: Role, locale: RoleSlugLocale) {
  return {
    pathname: paths.roleDetails,
    params: {
      roleRef: role.type ? roleTypeToSlug(role.type, locale) : role.id,
    },
  } as const;
}

function RoleDetailLoadingShell() {
  const t = useTranslations('components.pages.roleDetail');
  return (
    <main className="mx-auto flex w-full max-w-[1100px] flex-col gap-8 px-6 py-[100px]" aria-busy="true">
      <span className="sr-only">{t('loading')}</span>
      <nav className="flex flex-wrap items-center gap-2" aria-hidden>
        <TextSkeleton variant="body-xs" className="max-w-28" />
        <TextSkeleton variant="body-xs" className="max-w-3" lineClassName="max-w-2" />
        <TextSkeleton variant="body-xs" className="max-w-24" />
        <TextSkeleton variant="body-xs" className="max-w-3" lineClassName="max-w-2" />
        <TextSkeleton variant="body-xs" className="max-w-36" />
      </nav>

      <div className="grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,340px)_1fr]">
        <Card className="isolate overflow-hidden" liftOnHover={false} fullfilled>
          <div className="relative aspect-3/4 w-full rounded-t-sm bg-neutral-800/80">
            <div className="absolute inset-x-0 bottom-0 z-10 flex flex-col gap-3 p-5">
              <TextSkeleton variant="tag" className="max-w-32" />
              <div className="grid grid-cols-2 gap-2">
                <GlassPanel className="shadow-none! flex flex-col gap-2 rounded-xs border border-primary/15 p-2.5">
                  <TextSkeleton variant="subtitle" className="max-w-8 mx-auto" />
                  <TextSkeleton variant="body-xs" className="max-w-full" lineClassName="max-w-20 mx-auto" />
                </GlassPanel>
                <GlassPanel className="shadow-none! flex flex-col gap-2 rounded-xs border border-primary/15 p-2.5">
                  <TextSkeleton variant="subtitle" className="max-w-8 mx-auto" />
                  <TextSkeleton variant="body-xs" className="max-w-full" lineClassName="max-w-20 mx-auto" />
                </GlassPanel>
              </div>
            </div>
          </div>
        </Card>

        <div className="flex flex-col gap-6">
          <div className="flex flex-col gap-3">
            <TextSkeleton variant="body-xs" className="max-w-56" />
            <TextSkeleton variant="heading-2" className="max-w-full" lineClassName="max-w-md" />
          </div>

          <GlassPanel className="flex flex-col gap-3 p-5 sm:p-6">
            <div className="flex items-center gap-2">
              <TextSkeleton variant="body-xs" className="max-w-32 shrink-0" />
              <Divider variant="secondary" className="min-h-px min-w-0 flex-1" />
            </div>
            <TextSkeleton variant="body" lines={5} />
          </GlassPanel>

          <Divider variant="secondary" className="w-full" />

          <GlassPanel className="flex flex-col gap-3 p-5 sm:p-6">
            <div className="flex items-center gap-2">
              <TextSkeleton variant="body-xs" className="max-w-28 shrink-0" />
              <Divider variant="secondary" className="min-h-px min-w-0 flex-1" />
            </div>
            <TextSkeleton variant="body" lines={3} />
          </GlassPanel>

          <GlassPanel className="flex flex-col gap-3 p-5 sm:p-6">
            <div className="flex items-center gap-2">
              <TextSkeleton variant="body-xs" className="max-w-40 shrink-0" />
              <Divider variant="secondary" className="min-h-px min-w-0 flex-1" />
            </div>
            <div className="flex flex-wrap gap-2">
              <TextSkeleton variant="tag" className="max-w-24" />
              <TextSkeleton variant="tag" className="max-w-28" />
            </div>
          </GlassPanel>
        </div>
      </div>

      <div className="flex flex-col gap-4 sm:flex-row sm:justify-between sm:gap-4">
        <Card className="min-h-[88px] flex-1 flex-col gap-2 p-4" liftOnHover={false}>
          <TextSkeleton variant="body-xs" className="max-w-32" />
          <TextSkeleton variant="subtitle" className="max-w-48" />
        </Card>
        <Card className="min-h-[88px] flex-1 flex-col items-end gap-2 p-4" liftOnHover={false}>
          <TextSkeleton variant="body-xs" className="max-w-28" lineClassName="ml-auto max-w-28" />
          <TextSkeleton variant="subtitle" className="max-w-48" lineClassName="ml-auto max-w-48" />
        </Card>
      </div>
    </main>
  );
}

const RoleDetailClient = ({ roleRef }: Props) => {
  const t = useTranslations('components.pages.roleDetail');
  const troles = useTranslations('roles');
  const locale = useLocale() as RoleSlugLocale;
  const { getRole, role, roleLoading, roleList, getAllRoles } = useRole();
  useEffect(() => {
    getRole({ ref: roleRef });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [roleRef]);

  useEffect(() => {
    if (roleList.length === 0) {
      getAllRoles();
    }
  }, [roleList, getAllRoles]);

  const previousRole = useMemo(() => {
    if (!role?.id || roleList.length === 0) return null;
    const currentRoleIndexOnList = roleList.findIndex((roleFromList) => roleFromList.id === role.id);
    return roleList[currentRoleIndexOnList - 1] ?? roleList[roleList.length - 1] ?? null;
  }, [roleList, role]);

  const nextRole = useMemo(() => {
    if (!role?.id || roleList.length === 0) return null;
    const currentRoleIndexOnList = roleList.findIndex((roleFromList) => roleFromList.id === role.id);
    return roleList[currentRoleIndexOnList + 1] ?? roleList[0] ?? null;
  }, [roleList, role]);

  const primaryTeam = role?.teams?.[0];
  const campLabel = primaryTeam ? troles(primaryTeam) : '-';

  if (roleLoading) {
    return <RoleDetailLoadingShell />;
  }

  if (!role) {
    return (
      <main className="mx-auto flex w-full max-w-[1100px] flex-col px-6 py-[100px]">
        <Card className="w-full p-8" liftOnHover={false} fullfilled>
          <Typography tag="p" variant="body" className="text-center">
            {t('notFound')}
          </Typography>
        </Card>
      </main>
    );
  }

  const cardTeamVariant = (primaryTeam ?? GameTeamEnum.VILLAGE) as RoleCardVariant;

  return (
    <main className="mx-auto flex w-full max-w-[1100px] flex-col gap-8 px-6 py-[100px]">
      <nav className="flex flex-wrap items-center gap-2">
        <Link href={paths.roles} className="transition-colors hover:text-primary">
          <Typography tag="span" variant="body-xs" bold uppercase textColor="neutral-500">
            {t('breadcrumbBestiary')}
          </Typography>
        </Link>
        {primaryTeam && (
          <>
            <Typography tag="span" variant="body-xs" textColor="neutral-600">
              /
            </Typography>
            <Tag label={campLabel} variant={gameTeamVariant(primaryTeam)} />
          </>
        )}
        <Typography tag="span" variant="body-xs" textColor="neutral-600">
          /
        </Typography>
        <Typography tag="span" variant="body-xs" bold uppercase textColor="neutral-400">
          {role.name}
        </Typography>
      </nav>

      <div className="grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,340px)_1fr]">
        <Card type="role" variant={cardTeamVariant} className="isolate overflow-hidden" liftOnHover={false} fullfilled>
          <div className="relative aspect-3/4 w-full rounded-t-sm">
            {role.picture ? (
              <Image src={getImagePath(role.picture)} alt={role.name ?? ''} fill unoptimized className="object-cover" />
            ) : (
              <div className="flex size-full items-center justify-center bg-linear-to-br from-secondary/60 to-dark">
                <Typography tag="span" variant="heading-1" className="opacity-40">
                  ?
                </Typography>
              </div>
            )}
            <div className="absolute inset-0 bg-linear-to-b from-transparent via-transparent to-black/80" />
            <div className="absolute inset-x-0 bottom-0 z-10 flex flex-col gap-3 p-5">
              {primaryTeam && <Tag label={campLabel} variant={gameTeamVariant(primaryTeam)} />}
              <div className="grid grid-cols-2 gap-2">
                {role.minPlayers != null && (
                  <GlassPanel className="shadow-none! flex flex-col gap-0.5 rounded-xs border border-primary/15 p-2.5 text-center">
                    <Typography tag="span" variant="subtitle" bold className="font-title leading-none">
                      {role.minPlayers}
                    </Typography>
                    <Typography
                      tag="span"
                      variant="body-xs"
                      bold
                      uppercase
                      textColor="neutral-500"
                      className="text-[0.58rem] tracking-wide"
                    >
                      {t('minPlayers')}
                    </Typography>
                  </GlassPanel>
                )}
                {role.maxPerGame != null && (
                  <GlassPanel className="shadow-none! flex flex-col gap-0.5 rounded-xs border border-primary/15 p-2.5 text-center">
                    <Typography tag="span" variant="subtitle" bold className="font-title leading-none">
                      {role.maxPerGame}
                    </Typography>
                    <Typography
                      tag="span"
                      variant="body-xs"
                      bold
                      uppercase
                      textColor="neutral-500"
                      className="text-[0.58rem] tracking-wide"
                    >
                      {t('maxPerGame')}
                    </Typography>
                  </GlassPanel>
                )}
              </div>
            </div>
          </div>
        </Card>

        <div className="flex flex-col gap-6">
          <div>
            <Typography tag="p" variant="body-xs" bold uppercase textColor="primary" className="mb-1 tracking-[0.16em]">
              {t('roleEyebrow', { camp: campLabel })}
            </Typography>
            <Typography tag="h1" variant="heading-2" bold className="mb-3 font-title">
              {role.name}
            </Typography>
          </div>

          {role.description && (
            <GlassPanel className="flex flex-col gap-3 p-5 sm:p-6">
              <div className="flex items-center gap-2">
                <Typography
                  tag="h2"
                  variant="body-xs"
                  bold
                  uppercase
                  textColor="neutral-400"
                  className="shrink-0 tracking-[0.14em]"
                >
                  {t('description')}
                </Typography>
                <Divider variant="secondary" className="min-h-px min-w-0 flex-1" />
              </div>
              <Typography tag="p" variant="body" textColor="neutral-300" className="leading-relaxed">
                {role.description}
              </Typography>
            </GlassPanel>
          )}

          <Divider variant="secondary" className="w-full" />

          {role.ability && (
            <GlassPanel className="border-primary/25 bg-primary/10 flex flex-col gap-3 p-5 sm:p-6">
              <div className="flex items-center gap-2">
                <Typography
                  tag="h2"
                  variant="body-xs"
                  bold
                  uppercase
                  textColor="primary"
                  className="mb-2 tracking-[0.12em]"
                >
                  {t('ability')}
                </Typography>
                <Divider variant="secondary" className="min-h-px min-w-0 flex-1" />
              </div>

              <Typography tag="p" variant="body" textColor="neutral-200" className="leading-relaxed">
                {role.ability}
              </Typography>
            </GlassPanel>
          )}

          {role.teams && role.teams.length > 0 && (
            <GlassPanel className="flex flex-col gap-3 p-5 sm:p-6">
              <div className="flex items-center gap-2">
                <Typography
                  tag="h2"
                  variant="body-xs"
                  bold
                  uppercase
                  textColor="neutral-400"
                  className="shrink-0 tracking-[0.14em]"
                >
                  {t('compatibleCamps')}
                </Typography>
                <Divider variant="secondary" className="min-h-px min-w-0 flex-1" />
              </div>
              <div className="flex flex-wrap gap-2">
                {role.teams.map((team) => (
                  <Tag key={team} label={troles(team)} variant={gameTeamVariant(team)} />
                ))}
              </div>
            </GlassPanel>
          )}
        </div>
      </div>
      <div className="flex justify-between gap-4">
        {previousRole && (
          <Card
            type="role"
            variant={previousRole.teams?.[0] as RoleCardVariant}
            href={roleListHref(previousRole, locale)}
            className="w-full! flex-col items-start justify-start"
            liftOnHover={false}
          >
            <div className="flex items-start justify-start gap-2">
              <ArrowLeftIcon className="w-4 h-4 mb-1 text-neutral-400" />
              <Typography
                tag="span"
                variant="body-xs"
                bold
                uppercase
                textColor="neutral-400"
                className="tracking-[0.16em]"
              >
                {t('previousRole')}
              </Typography>
            </div>
            <Typography tag="h1" variant="subtitle" bold className="font-title">
              {previousRole.name}
            </Typography>
          </Card>
        )}
        {nextRole && (
          <Card
            type="role"
            variant={nextRole.teams?.[0] as RoleCardVariant}
            href={roleListHref(nextRole, locale)}
            className="w-full! flex-col items-end justify-start"
            liftOnHover={false}
          >
            <div className="flex items-end justify-end gap-2">
              <Typography
                tag="span"
                variant="body-xs"
                bold
                uppercase
                textColor="neutral-400"
                className="tracking-[0.16em]"
              >
                {t('nextRole')}
              </Typography>
              <ArrowRightIcon className="w-4 h-4 mb-1 text-neutral-400" />
            </div>
            <Typography tag="h1" variant="subtitle" bold className="font-title">
              {nextRole.name}
            </Typography>
          </Card>
        )}
      </div>
    </main>
  );
};

export default RoleDetailClient;
