'use client';

import RoleAdminForm from './role-admin-form';
import type { Role } from '@/utils/types';

type Props = {
  role: Role;
  className?: string;
};

const RoleUpdateForm = ({ role, className }: Props) => {
  return <RoleAdminForm mode="update" role={role} className={className} />;
};

export default RoleUpdateForm;
