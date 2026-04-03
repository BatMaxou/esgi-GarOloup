'use client';

import { createContext, ReactNode, useContext, useState } from 'react';
import { toast } from 'react-toastify';
import { useTranslations } from 'next-intl';

import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { Role } from '@/utils/types';
import { GameTeamEnum } from '@/utils/enums';

type Props = {
  children: ReactNode;
};

type RoleContextType = {
  roleList: Role[];
  getAllRoles: () => void;
  roleListLoading: boolean;
  filteredRoleList: Role[];
  setFilteredRoleList: (filteredRoleList: Role[]) => void;
  gameTeamFilters: GameTeamEnum[];
  gameTeamFiltersLoading: boolean;
  getAllGameTeamFilters: () => void;
};

export const RoleContext = createContext<RoleContextType | undefined>(undefined);

export const RoleProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const t = useTranslations('contexts.role');
  const [roleList, setRoleList] = useState<Role[]>([]);
  const [roleListLoading, setRoleListLoading] = useState<boolean>(true);
  const [filteredRoleList, setFilteredRoleList] = useState<Role[]>([]);
  const [gameTeamFilters, setGameTeamFilters] = useState<GameTeamEnum[]>([]);
  const [gameTeamFiltersLoading, setGameTeamFiltersLoading] = useState<boolean>(true);

  const getAllRoles = async () => {
    setRoleListLoading(true);
    await apiClient.role.getAll().then((roles) => {
      if (roles instanceof ApiClientError) {
        toast.error(t('roleListError'));
        setRoleListLoading(false);
        return;
      }
      setRoleList(roles);
    });
    setRoleListLoading(false);
  };

  const getAllGameTeamFilters = async () => {
    setGameTeamFiltersLoading(true);
    await apiClient.filter.getGameTeamFilters().then((filters) => {
      if (filters instanceof ApiClientError) {
        toast.error(t('gameTeamFiltersError'));
        setGameTeamFiltersLoading(false);
        return;
      }
      setGameTeamFilters(filters);
    });
    setGameTeamFiltersLoading(false);
  };

  return (
    <RoleContext.Provider
      value={{
        roleList,
        getAllRoles,
        roleListLoading,
        filteredRoleList,
        setFilteredRoleList,
        gameTeamFilters,
        gameTeamFiltersLoading,
        getAllGameTeamFilters,
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
