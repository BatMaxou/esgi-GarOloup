import { ReactNode } from 'react';
import { RoleProvider } from '@/contexts/role-context';

type Props = {
  children: ReactNode;
};

const RolesLayout = ({ children }: Props) => {
  return <RoleProvider>{children}</RoleProvider>;
};

export default RolesLayout;
