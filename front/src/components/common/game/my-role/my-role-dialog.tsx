'use client';

import Image from 'next/image';
import { useTranslations } from 'next-intl';

import Dialog from '@/components/ui/molecules/dialog';
import Typography from '@/components/ui/atoms/typography';
import Divider from '@/components/ui/atoms/divider';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import TextSkeleton from '@/components/ui/atoms/skeleton';
import Tag from '@/components/ui/molecules/tag';
import { getImagePath } from '@/utils/getImagePath';
import { gameTeamVariant } from '@/utils/variants';
import { GameTeamEnum } from '@/utils/enums';
import type { Role } from '@/utils/types';

type Props = {
  open: boolean;
  setOpen: (open: boolean) => void;
  role: Role | null;
  loading: boolean;
  team?: GameTeamEnum;
};

const MyRoleDialog = ({ open, setOpen, role, loading, team }: Props) => {
  const t = useTranslations('components.common.game.myRole');
  const troles = useTranslations('roles');

  const primaryTeam = team ?? role?.teams?.[0];
  const campLabel = primaryTeam ? troles(primaryTeam) : undefined;

  return (
    <Dialog open={open} setOpen={setOpen} title={t('title')} size="md">
      {loading && !role ? (
        <div className="flex flex-col gap-4">
          <TextSkeleton variant="heading-3" className="max-w-40" />
          <TextSkeleton variant="body" lines={4} />
        </div>
      ) : role ? (
        <div className="flex flex-col gap-5">
          <div className="flex items-center gap-4">
            <div className="relative h-24 w-20 shrink-0 overflow-hidden rounded-xs border border-primary/15">
              {role.picture ? (
                <Image
                  src={getImagePath(role.picture)}
                  alt={role.name ?? ''}
                  fill
                  unoptimized
                  className="object-cover"
                />
              ) : (
                <div className="flex size-full items-center justify-center bg-linear-to-br from-secondary/60 to-dark">
                  <Typography tag="span" variant="heading-2" className="opacity-40">
                    ?
                  </Typography>
                </div>
              )}
            </div>
            <div className="flex flex-col gap-2">
              <Typography tag="h3" variant="heading-3" bold className="font-title">
                {role.name}
              </Typography>
              {primaryTeam && campLabel && <Tag label={campLabel} variant={gameTeamVariant(primaryTeam)} />}
            </div>
          </div>

          {role.description && (
            <GlassPanel className="flex flex-col gap-2 p-4">
              <Typography
                tag="h3"
                variant="body-xs"
                bold
                uppercase
                textColor="neutral-400"
                className="tracking-[0.14em]"
              >
                {t('description')}
              </Typography>
              <Divider variant="secondary" className="w-full" />
              <Typography tag="p" variant="body-sm" textColor="neutral-300" className="leading-relaxed">
                {role.description}
              </Typography>
            </GlassPanel>
          )}

          {role.ability && (
            <GlassPanel className="border-primary/25 bg-primary/10 flex flex-col gap-2 p-4">
              <Typography tag="h3" variant="body-xs" bold uppercase textColor="primary" className="tracking-[0.12em]">
                {t('ability')}
              </Typography>
              <Divider variant="secondary" className="w-full" />
              <Typography tag="p" variant="body-sm" textColor="neutral-200" className="leading-relaxed">
                {role.ability}
              </Typography>
            </GlassPanel>
          )}
        </div>
      ) : (
        <Typography tag="p" variant="body" className="text-center">
          {t('notFound')}
        </Typography>
      )}
    </Dialog>
  );
};

export default MyRoleDialog;
