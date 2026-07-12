'use client';

import { createContext, ReactNode, useContext, useMemo, useState } from 'react';
import { toast } from 'react-toastify';
import { useTranslations } from 'next-intl';

import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { Role, RolePlayable } from '@/utils/types';
import { GameTeamEnum } from '@/utils/enums';
import { slugToRoleType } from '@/utils/roleSlug';
import type { RolePayload } from '@/components/common/form/admin/role-form-shared';

type Props = {
  children: ReactNode;
};

type GetRoleParams = {
  ref: string;
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
  createRole: (data: RolePayload) => Promise<Role | null>;
  createRoleLoading: boolean;
  updateRole: (id: string, data: RolePayload) => Promise<Role | null>;
  updateRoleLoading: boolean;
  uploadRolePicture: (id: string, picture: File) => Promise<Role | null>;
  uploadRolePictureLoading: boolean;
};

const isRolePlayable = (role: Role): role is RolePlayable => Boolean(role.type && role.name);

const upsertRoleInList = (roles: Role[], updatedRole: Role): Role[] => {
  const existingIndex = roles.findIndex((item) => item.id === updatedRole.id);
  if (existingIndex === -1) {
    return [...roles, updatedRole];
  }

  return roles.map((item) => (item.id === updatedRole.id ? updatedRole : item));
};

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
  const [createRoleLoading, setCreateRoleLoading] = useState(false);
  const [updateRoleLoading, setUpdateRoleLoading] = useState(false);
  const [uploadRolePictureLoading, setUploadRolePictureLoading] = useState(false);

  const getAllRoles = async () => {
    setRoleListLoading(true);
    await apiClient.role.getAll().then((roles) => {
      if (roles instanceof ApiClientError) {
        toast.error(t('roleListError'));
        setRoleListLoading(false);
        return;
      }
      setRoleList(roles);
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

  const syncRole = (updatedRole: Role) => {
    setRoleList((current) => upsertRoleInList(current, updatedRole));
    setHydratedRoleList((current) => upsertRoleInList(current, updatedRole));
    setFilteredRoleList((current) => upsertRoleInList(current, updatedRole));
    setRole((current) => (current?.id === updatedRole.id ? updatedRole : current));
  };

  const createRole = async (data: RolePayload): Promise<Role | null> => {
    setCreateRoleLoading(true);

    const result = await apiClient.role.create(data);
    if (result instanceof ApiClientError) {
      toast.error(t('roleCreateError'));
      setCreateRoleLoading(false);
      return null;
    }

    syncRole(result);
    toast.success(t('roleCreateSuccess'));
    setCreateRoleLoading(false);
    return result;
  };

  const updateRole = async (id: string, data: RolePayload): Promise<Role | null> => {
    setUpdateRoleLoading(true);

    const result = await apiClient.role.update(id, data);
    if (result instanceof ApiClientError) {
      toast.error(t('roleUpdateError'));
      setUpdateRoleLoading(false);
      return null;
    }

    syncRole(result);
    toast.success(t('roleUpdateSuccess'));
    setUpdateRoleLoading(false);
    return result;
  };

  const uploadRolePicture = async (id: string, picture: File): Promise<Role | null> => {
    setUploadRolePictureLoading(true);

    const formData = new FormData();
    formData.append('picture', picture);

    const result = await apiClient.role.updateFiles(id, formData);
    if (result instanceof ApiClientError) {
      toast.error(t('rolePictureUploadError'));
      setUploadRolePictureLoading(false);
      return null;
    }

    syncRole(result);
    setUploadRolePictureLoading(false);
    return result;
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
        createRole,
        createRoleLoading,
        updateRole,
        updateRoleLoading,
        uploadRolePicture,
        uploadRolePictureLoading,
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
