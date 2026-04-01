'use client';

import { createContext, ReactNode, useContext, useState } from 'react';
import { toast } from 'react-toastify';
import { useTranslations } from 'next-intl';

import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { Role } from '@/utils/types';

type Props = {
  children: ReactNode;
};

type RoleContextType = {
  roleList: Role[];
  getAllRoles: () => void;
  roleListLoading: boolean;
};

export const RoleContext = createContext<RoleContextType | undefined>(undefined);

export const RoleProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const t = useTranslations('contexts.role');
  const [roleList, setRoleList] = useState<Role[]>([]);
  const [roleListLoading, setRoleListLoading] = useState<boolean>(true);

  const getAllRoles = () => {
    setRoleListLoading(true);
    apiClient.role.getAll().then((roles) => {
      if (roles instanceof ApiClientError) {
        toast.error(t('roleListError'));
        return;
      }
      setRoleList(roles);
      setRoleListLoading(false);
    });
  }

  return (
    <RoleContext.Provider
      value={{
        roleList,
        getAllRoles,
        roleListLoading
      }}
    >
      {children}
    </RoleContext.Provider>
  );
};

export const useRole = () => {
  const context = useContext(RoleContext);
  if (!context) {
    throw new Error('useRole must be used within an RoleProvider');
  }

  return context;
};
