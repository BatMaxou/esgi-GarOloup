'use client';

import { createContext, ReactNode, useContext, useMemo, useState } from 'react';
import { toast } from 'react-toastify';
import { useTranslations } from 'next-intl';

import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { Role, RolePlayable } from '@/utils/types';
import { GameTeamEnum } from '@/utils/enums';
import { slugToRoleType } from '@/utils/roleSlug';

type Props = {
  children: ReactNode;
};

type RoleContextType = {
  roleList: Role[];
  playableRoleList: RolePlayable[];
  getAllRoles: () => void;
  roleListLoading: boolean;
  getRole: (params: GetRoleParams) => void;
  roleLoading: boolean;
  role: Role | null;
  filteredRoleList: Role[];
  setFilteredRoleList: (filteredRoleList: Role[]) => void;
  gameTeamFilters: GameTeamEnum[];
  gameTeamFiltersLoading: boolean;
  getAllGameTeamFilters: () => void;
};

type GetRoleParams = {
  ref: string;
};

const isRolePlayable = (role: Role): role is RolePlayable => Boolean(role.type && role.name);

export const RoleContext = createContext<RoleContextType | undefined>(undefined);

export const RoleProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const t = useTranslations('contexts.role');
  const [roleList, setRoleList] = useState<Role[]>([]);
  const [roleListLoading, setRoleListLoading] = useState<boolean>(true);

  const playableRoleList = useMemo(() => roleList.filter(isRolePlayable), [roleList]);
  const [filteredRoleList, setFilteredRoleList] = useState<Role[]>([]);
  const [hydratedRoleList, setHydratedRoleList] = useState<Role[]>([]);
  const [role, setRole] = useState<Role | null>(null);
  const [roleLoading, setRoleLoading] = useState<boolean>(true);
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
      setRoleList(roles as Role[]);
      setRoleListLoading(false);
    });
    return;
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

  const getRole = async (params: GetRoleParams) => {
    setRoleLoading(true);

    const { ref } = params;
    let roleRef = ref;

    if (!roleRef || roleRef === '') {
      toast.error(t('roleNotFound'));
      setRole(null);
      setRoleLoading(false);
      return;
    }

    const isRoleAlreadyFetched = hydratedRoleList.find((role) => role.id === roleRef || role.type === roleRef);

    if (isRoleAlreadyFetched) {
      setRole(isRoleAlreadyFetched);
      setRoleLoading(false);
      return;
    }

    const roleType = slugToRoleType(roleRef);
    if (roleType !== null) {
      let list = roleList;
      if (list.length === 0) {
        const fetched = await apiClient.role.getAll();
        if (fetched instanceof ApiClientError) {
          toast.error(t('roleListError'));
          setRole(null);
          setRoleLoading(false);
          return;
        }
        setRoleList(fetched);
        list = fetched;
      }

      const role = list.find((role) => role.type === roleType);
      if (!role) {
        toast.error(t('roleNotFound'));
        setRole(null);
        setRoleLoading(false);
        return;
      }

      roleRef = role.id;
    }

    const result = await apiClient.role.get(roleRef);
    if (result instanceof ApiClientError) {
      toast.error(t('roleNotFound'));
      setRole(null);
      setRoleLoading(false);
      return;
    }

    setRole(result);
    if (!hydratedRoleList.includes(result)) {
      setHydratedRoleList([...hydratedRoleList, result]);
    }
    setRoleLoading(false);
  };

  return (
    <RoleContext.Provider
      value={{
        roleList,
        playableRoleList,
        getAllRoles,
        roleListLoading,
        getRole,
        roleLoading,
        role,
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
