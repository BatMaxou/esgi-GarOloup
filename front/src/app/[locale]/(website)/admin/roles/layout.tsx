import { ReactNode } from 'react';

import { RoleProvider } from '@/contexts/role-context';

type Props = {
  children: ReactNode;
};

const AdminRolesLayout = ({ children }: Props) => {
  return <RoleProvider>{children}</RoleProvider>;
};

export default AdminRolesLayout;
