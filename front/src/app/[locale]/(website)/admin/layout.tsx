import { ReactNode } from 'react';

import AdminForbiddenRedirect from '@/components/common/layout/admin-forbidden-redirect';
import { hasAdminRole } from '@/lib/auth/user-fields';
import { getSession } from '@/utils/server/clients';

type Props = {
  children: ReactNode;
};

const AdminLayout = async ({ children }: Props) => {
  const session = await getSession();
  const userRoles = session?.user && 'roles' in session.user ? session.user.roles : undefined;
  const isAdmin = hasAdminRole(userRoles);

  if (!isAdmin) {
    return <AdminForbiddenRedirect />;
  }

  return children;
};

export default AdminLayout;
