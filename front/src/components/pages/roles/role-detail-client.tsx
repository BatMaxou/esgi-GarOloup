'use client';

import { useEffect } from 'react';

import { useRole } from '@/contexts/role-context';
import { GameRoleEnum } from '@/utils/enums';

type Props = {
  roleType: GameRoleEnum;
};

const RoleDetailClient = ({ roleType }: Props) => {
  const { getRole, role, roleLoading } = useRole();

  useEffect(() => {
    getRole({ slug: roleType });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [roleType]);

  if (roleLoading) return <div>Chargement…</div>;
  if (!role) return <div>Rôle introuvable.</div>;

  return <div>{role.name}</div>;
};

export default RoleDetailClient;
