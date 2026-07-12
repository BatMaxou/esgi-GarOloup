'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import { useLocale, useTranslations } from 'next-intl';
import { ArrowLeft } from 'lucide-react';

import Button from '@/components/ui/molecules/button';
import Dialog from '@/components/ui/molecules/dialog';
import Dot from '@/components/ui/atoms/dot';
import Tooltip from '@/components/ui/atoms/tooltip';
import TextSkeleton from '@/components/ui/atoms/skeleton';
import Typography from '@/components/ui/atoms/typography';
import Tag from '@/components/ui/molecules/tag';
import SelectInput from '@/components/ui/molecules/select-input';
import DataTable, {
  compareNumbersAsc,
  compareStringsAsc,
  type DataTableColumn,
} from '@/components/ui/organisms/data-table';
import { useRole } from '@/contexts/role-context';
import { paths } from '@/utils/paths';
import { GameRoleEnum } from '@/utils/enums';
import { gameTeamVariant } from '@/utils/variants';
import { roleTypeToSlug, type RoleSlugLocale } from '@/utils/roleSlug';
import type { Role } from '@/utils/types';

const AdminRolesClient = () => {
  const t = useTranslations('components.pages.admin.roles');
  const troles = useTranslations('roles');
  const tRoleTypes = useTranslations('components.common.game.nightRecap.roles');
  const locale = useLocale() as RoleSlugLocale;
  const { roleList, roleListLoading, getAllRoles, updateRole, updateRoleLoading } = useRole();
  const [disableModalOpen, setDisableModalOpen] = useState(false);
  const [roleToDisable, setRoleToDisable] = useState<Role | null>(null);
  const [enableModalOpen, setEnableModalOpen] = useState(false);
  const [roleToEnable, setRoleToEnable] = useState<Role | null>(null);
  const [selectedEnableType, setSelectedEnableType] = useState('');

  useEffect(() => {
    getAllRoles();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleDisableClick = useCallback((role: Role) => {
    setRoleToDisable(role);
    setDisableModalOpen(true);
  }, []);

  const handleConfirmDisable = useCallback(async () => {
    if (!roleToDisable) {
      return;
    }

    const result = await updateRole(roleToDisable.id, { type: null });
    if (result) {
      setDisableModalOpen(false);
      setRoleToDisable(null);
    }
  }, [roleToDisable, updateRole]);

  const handleCloseDisableModal = useCallback(
    (open: boolean) => {
      if (updateRoleLoading) {
        return;
      }

      setDisableModalOpen(open);
      if (!open) {
        setRoleToDisable(null);
      }
    },
    [updateRoleLoading]
  );

  const usedRoleTypes = useMemo(
    () =>
      new Set(
        roleList.filter((role) => role.type && role.id !== roleToEnable?.id).map((role) => role.type as GameRoleEnum)
      ),
    [roleList, roleToEnable]
  );

  const enableTypeOptions = useMemo(
    () => [
      { value: '', label: t('enableRoleModal.typePlaceholder') },
      ...Object.values(GameRoleEnum)
        .filter((roleType) => !usedRoleTypes.has(roleType))
        .map((roleType) => ({
          value: roleType,
          label: tRoleTypes(roleType),
        })),
    ],
    [t, tRoleTypes, usedRoleTypes]
  );

  const handleEnableClick = useCallback((role: Role) => {
    setRoleToEnable(role);
    setSelectedEnableType('');
    setEnableModalOpen(true);
  }, []);

  const handleConfirmEnable = useCallback(async () => {
    if (!roleToEnable || !selectedEnableType) {
      return;
    }

    const result = await updateRole(roleToEnable.id, { type: selectedEnableType as GameRoleEnum });
    if (result) {
      setEnableModalOpen(false);
      setRoleToEnable(null);
      setSelectedEnableType('');
    }
  }, [roleToEnable, selectedEnableType, updateRole]);

  const handleCloseEnableModal = useCallback(
    (open: boolean) => {
      if (updateRoleLoading) {
        return;
      }

      setEnableModalOpen(open);
      if (!open) {
        setRoleToEnable(null);
        setSelectedEnableType('');
      }
    },
    [updateRoleLoading]
  );

  const isRoleEnabled = useCallback((role: Role) => Boolean(role.type), []);

  const columns = useMemo<DataTableColumn<Role>[]>(
    () => [
      {
        id: 'name',
        header: t('column.name'),
        sortable: true,
        compareAscending: (left, right) => compareStringsAsc(left.name ?? '', right.name ?? ''),
        getFilterText: (role) => role.name ?? '',
        cell: (role) => (
          <Typography tag="span" variant="body" bold>
            {role.name ?? t('unnamedRole')}
          </Typography>
        ),
      },
      {
        id: 'type',
        header: t('column.type'),
        sortable: true,
        compareAscending: (left, right) => compareStringsAsc(left.type ?? '', right.type ?? ''),
        getFilterText: (role) => role.type ?? '',
        cell: (role) => (
          <Typography tag="span" variant="body-sm" textColor="neutral-300">
            {role.type ?? '—'}
          </Typography>
        ),
      },
      {
        id: 'status',
        header: t('column.status'),
        sortable: true,
        compareAscending: (left, right) => compareNumbersAsc(isRoleEnabled(left) ? 1 : 0, isRoleEnabled(right) ? 1 : 0),
        getFilterText: (role) => (isRoleEnabled(role) ? t('statusEnabled') : t('statusDisabled')),
        cell: (role) => {
          const enabled = isRoleEnabled(role);

          return (
            <span className="inline-flex items-center gap-2">
              <Dot variant={enabled ? 'live' : 'inactive'} />
              <Typography tag="span" variant="body-sm" textColor={enabled ? 'success' : 'error'}>
                {enabled ? t('statusEnabled') : t('statusDisabled')}
              </Typography>
            </span>
          );
        },
      },
      {
        id: 'teams',
        header: t('column.teams'),
        getFilterText: (role) => role.teams?.map((team) => troles(team)).join(' ') ?? '',
        cell: (role) =>
          role.teams && role.teams.length > 0 ? (
            <div className="flex flex-wrap gap-1.5">
              {role.teams.map((team) => (
                <Tag key={team} variant={gameTeamVariant(team)} label={troles(team)} />
              ))}
            </div>
          ) : (
            <span className="text-neutral-500">—</span>
          ),
      },
      {
        id: 'players',
        header: t('column.players'),
        sortable: true,
        compareAscending: (left, right) => compareNumbersAsc(left.minPlayers ?? 0, right.minPlayers ?? 0),
        getFilterText: (role) => `${role.minPlayers ?? 0}/${role.maxPerGame ?? 0}`,
        cell: (role) => (
          <Typography tag="span" variant="body-sm">
            {t('playersRange', { min: role.minPlayers ?? 0, max: role.maxPerGame ?? 0 })}
          </Typography>
        ),
      },
      {
        id: 'actions',
        header: t('column.actions'),
        headerAlign: 'end',
        tdClassName: 'text-right',
        cell: (role) => {
          const enabled = isRoleEnabled(role);

          return (
            <div className="inline-flex items-center justify-end gap-2">
              <Tooltip content={t('viewPublicRole')} placement="left">
                <Button
                  variant="neutral"
                  size="xs"
                  leftIcon="eye"
                  aria-label={t('viewPublicRole')}
                  asLink
                  href={{
                    pathname: paths.roleDetails,
                    params: { roleRef: role.type ? roleTypeToSlug(role.type, locale) : role.id },
                  }}
                />
              </Tooltip>
              <Tooltip content={t('editRole')} placement="left">
                <Button
                  variant="accent"
                  size="xs"
                  leftIcon="pencil"
                  aria-label={t('editRole')}
                  asLink
                  href={{ pathname: paths.adminRolesEdit, params: { roleId: role.id } }}
                />
              </Tooltip>
              <Tooltip content={enabled ? t('disableRole') : t('enableRole')} placement="left">
                <Button
                  type="button"
                  variant={enabled ? 'error' : 'success'}
                  size="xs"
                  leftIcon="turnOff"
                  aria-label={enabled ? t('disableRole') : t('enableRole')}
                  onClick={() => (enabled ? handleDisableClick(role) : handleEnableClick(role))}
                />
              </Tooltip>
            </div>
          );
        },
      },
    ],
    [t, troles, locale, handleDisableClick, handleEnableClick, isRoleEnabled]
  );

  return (
    <main className="mx-auto w-full max-w-[1100px] px-6 py-[100px]">
      <div className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <div className="mb-2 flex items-center gap-2">
            <ArrowLeft className="size-4" />
            <Button variant="text" size="sm" label={t('backToAdmin')} asLink href={paths.admin} />
          </div>
          <Typography tag="div" textColor="primary" bold>
            {t('eyebrow')}
          </Typography>
          <Typography tag="h1" variant="heading-2" bold>
            {t('title')}
          </Typography>
          <Typography tag="p" className="mt-2">
            {t('subtitle')}
          </Typography>
        </div>

        <div className="flex flex-wrap gap-2 mt-2">
          <Button variant="accent" size="sm" label={t('createRole')} asLink href={paths.adminRolesCreate} />
        </div>
      </div>

      <Typography tag="h2" variant="subtitle" bold className="mb-4">
        {t('listTitle')}
      </Typography>

      {roleListLoading ? (
        <div className="flex flex-col gap-3">
          <TextSkeleton className="h-20 w-full" />
          <TextSkeleton className="h-20 w-full" />
          <TextSkeleton className="h-20 w-full" />
        </div>
      ) : (
        <DataTable<Role>
          rows={roleList}
          columns={columns}
          getRowId={(role) => role.id}
          defaultSortColumnId="name"
          filter={{
            label: t('filterLabel'),
            placeholder: t('filterPlaceholder'),
          }}
          emptyMessage={
            <Typography variant="body-sm" textColor="neutral-500" className="py-8 text-center">
              {t('empty')}
            </Typography>
          }
          noMatchMessage={t('noMatchingFilter')}
        />
      )}

      <Dialog
        open={disableModalOpen}
        setOpen={handleCloseDisableModal}
        size="sm"
        title={t('disableRoleModal.title')}
        description={t('disableRoleModal.description', { name: roleToDisable?.name ?? t('unnamedRole') })}
      >
        <div className="flex flex-wrap justify-end gap-2">
          <Button
            type="button"
            variant="neutral"
            label={t('disableRoleModal.cancel')}
            onClick={() => handleCloseDisableModal(false)}
            disabled={updateRoleLoading}
          />
          <Button
            type="button"
            variant="error"
            label={t('disableRoleModal.confirm')}
            onClick={() => void handleConfirmDisable()}
            loading={updateRoleLoading}
            disabled={updateRoleLoading}
          />
        </div>
      </Dialog>

      <Dialog
        open={enableModalOpen}
        setOpen={handleCloseEnableModal}
        size="sm"
        title={t('enableRoleModal.title')}
        description={t('enableRoleModal.description', { name: roleToEnable?.name ?? t('unnamedRole') })}
      >
        <div className="mb-4">
          <SelectInput
            label={t('enableRoleModal.typeLabel')}
            name="enableRoleType"
            value={selectedEnableType}
            options={enableTypeOptions}
            onChange={(event) => setSelectedEnableType(event.target.value)}
          />
          {enableTypeOptions.length <= 1 && (
            <Typography tag="p" variant="body-xs" textColor="error" className="mt-2">
              {t('enableRoleModal.noTypeAvailable')}
            </Typography>
          )}
        </div>
        <div className="flex flex-wrap justify-end gap-2">
          <Button
            type="button"
            variant="neutral"
            label={t('enableRoleModal.cancel')}
            onClick={() => handleCloseEnableModal(false)}
            disabled={updateRoleLoading}
          />
          <Button
            type="button"
            variant="success"
            label={t('enableRoleModal.confirm')}
            onClick={() => void handleConfirmEnable()}
            loading={updateRoleLoading}
            disabled={updateRoleLoading || !selectedEnableType}
          />
        </div>
      </Dialog>
    </main>
  );
};

export default AdminRolesClient;
