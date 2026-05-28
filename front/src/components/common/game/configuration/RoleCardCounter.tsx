'use client';

import Typography from '@/components/ui/atoms/typography';
import Card, { RoleCardVariant } from '@/components/ui/molecules/card';
import NumberInput from '@/components/ui/molecules/number-input';
import { GameRoleEnum, GameTeamEnum } from '@/utils/enums';
import { getImagePath } from '@/utils/getImagePath';
import type { RoleEntry, RolePlayable } from '@/utils/types';
import Image from 'next/image';

export const getTotalRolesCount = (roles: RoleEntry[]) => roles.reduce((acc, entry) => acc + (entry.count ?? 0), 0);

export const incrementRoleInComposition = (
  roles: RoleEntry[],
  roleType: GameRoleEnum,
  playerCount: number
): RoleEntry[] | null => {
  if (getTotalRolesCount(roles) >= playerCount) {
    return null;
  }

  const existing = roles.find((thisRole: RoleEntry) => thisRole.role === roleType);

  if (existing) {
    return roles.map((thisRole: RoleEntry) =>
      thisRole.role === roleType ? { ...thisRole, count: (thisRole.count ?? 0) + 1 } : thisRole
    );
  }

  return [...roles, { role: roleType, count: 1 }];
};

export const decrementRoleInComposition = (roles: RoleEntry[], roleType: GameRoleEnum): RoleEntry[] | null => {
  const existing = roles.find((thisRole: RoleEntry) => thisRole.role === roleType);
  if (!existing || (existing.count ?? 0) <= 0) {
    return null;
  }

  if (existing.count === 1) {
    return roles.filter((thisRole: RoleEntry) => thisRole.role !== roleType);
  }

  return roles.map((thisRole: RoleEntry) =>
    thisRole.role === roleType ? { ...thisRole, count: (thisRole.count ?? 0) - 1 } : thisRole
  );
};

type Props = {
  role: RolePlayable;
  compositionRoles: RoleEntry[];
  playerCount: number;
  onCompositionChange: (roles: RoleEntry[]) => void;
};

const RoleCardCounter = ({ role, compositionRoles, playerCount, onCompositionChange }: Props) => {
  const count = compositionRoles.find((thisRole: RoleEntry) => thisRole.role === role.type)?.count ?? 0;
  const totalRolesCount = getTotalRolesCount(compositionRoles);
  const remainingSlots = playerCount - totalRolesCount;
  const maxCount = Math.min(role.maxPerGame ?? playerCount, count + remainingSlots);

  const handleIncrement = () => {
    const nextRoles = incrementRoleInComposition(compositionRoles, role.type, playerCount);
    if (nextRoles) {
      onCompositionChange(nextRoles);
    }
  };

  const handleDecrement = () => {
    const nextRoles = decrementRoleInComposition(compositionRoles, role.type);
    if (nextRoles) {
      onCompositionChange(nextRoles);
    }
  };

  return (
    <div className="flex flex-col justify-center items-center gap-4">
      <Card
        type="role"
        variant={(role.teams?.[0] ?? GameTeamEnum.VILLAGE) as RoleCardVariant}
        className="overflow-hidden min-h-48 w-full"
        fullfilled
        liftOnHover={false}
      >
        <div className="relative aspect-3/4 min-h-48 w-full">
          {role.picture ? (
            <Image src={getImagePath(role.picture)} alt={role.name ?? ''} fill unoptimized className="object-cover" />
          ) : (
            <div className="absolute inset-0 bg-dark/60" />
          )}
          <div className="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/80 via-black/40 to-transparent px-2 py-3">
            <Typography variant="body-sm" bold className="text-glow-dark text-center">
              {role.name}
            </Typography>
          </div>
        </div>
      </Card>
      <NumberInput
        key={`${role.type}-${count}`}
        name={`role-count-${role.type}`}
        defaultValue={count}
        min={0}
        max={maxCount}
        onIncrement={handleIncrement}
        onDecrement={handleDecrement}
      />
    </div>
  );
};

export default RoleCardCounter;
