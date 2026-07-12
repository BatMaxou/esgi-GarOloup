'use client';

import RoleAdminForm from './role-admin-form';

const RoleCreateForm = ({ className }: { className?: string }) => {
  return <RoleAdminForm mode="create" className={className} />;
};

export default RoleCreateForm;
