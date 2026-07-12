'use client';

import { useMemo } from 'react';
import { useFormik } from 'formik';
import { useTranslations } from 'next-intl';
import cn from 'classnames';

import Button from '@/components/ui/molecules/button';
import MultiSelectInput from '@/components/ui/molecules/multi-select-team-input';
import SelectInput from '@/components/ui/molecules/select-input';
import SwitchInput from '@/components/ui/molecules/switch-input';
import TextareaInput from '@/components/ui/molecules/textarea-input';
import TextInput from '@/components/ui/molecules/text-input';
import Typography from '@/components/ui/atoms/typography';
import { useRouter } from '@/i18n/navigation';
import { useRole } from '@/contexts/role-context';
import { paths } from '@/utils/paths';
import { GameRoleEnum, GameTeamEnum } from '@/utils/enums';
import { gameTeamVariant } from '@/utils/variants';
import type { Role } from '@/utils/types';

import { buildRolePayload, emptyRoleFormValues, mapRoleToFormValues, type RoleFormValues } from './role-form-shared';
import { getImagePath } from '@/utils/getImagePath';
import Image from 'next/image';

type Props = {
  mode: 'create' | 'update';
  role?: Role;
  className?: string;
};

const RoleAdminForm = ({ mode, role, className }: Props) => {
  const t = useTranslations(
    mode === 'create' ? 'components.pages.admin.rolesCreate' : 'components.pages.admin.rolesUpdate'
  );
  const troles = useTranslations('roles');
  const tRoleTypes = useTranslations('components.common.game.nightRecap.roles');
  const router = useRouter();
  const { createRole, createRoleLoading, updateRole, updateRoleLoading, uploadRolePicture, uploadRolePictureLoading } =
    useRole();
  const isSubmitting = createRoleLoading || updateRoleLoading || uploadRolePictureLoading;

  const roleTypeOptions = useMemo(
    () => [
      { value: '', label: t('fields.typePlaceholder') },
      ...Object.values(GameRoleEnum).map((roleType) => ({
        value: roleType,
        label: tRoleTypes(roleType),
      })),
    ],
    [t, tRoleTypes]
  );

  const teamOptions = useMemo(
    () =>
      Object.values(GameTeamEnum).map((team) => ({
        value: team,
        label: troles(team),
      })),
    [troles]
  );

  const initialValues = useMemo(
    () => (mode === 'update' && role ? mapRoleToFormValues(role) : emptyRoleFormValues),
    [mode, role]
  );

  const { values, errors, touched, submitCount, handleSubmit, handleChange, setFieldValue, setFieldError } =
    useFormik<RoleFormValues>({
      initialValues,
      enableReinitialize: mode === 'update',
      validate: (formValues) => {
        const validationErrors: Partial<Record<keyof RoleFormValues, string>> = {};

        if (!formValues.name.trim()) {
          validationErrors.name = t('errors.nameRequired');
        }

        if (!formValues.description.trim()) {
          validationErrors.description = t('errors.descriptionRequired');
        }

        if (formValues.isPlayable && !formValues.type) {
          validationErrors.type = t('errors.typeRequired');
        }

        return validationErrors;
      },
      onSubmit: async (formValues) => {
        const payload = buildRolePayload(formValues, mode);

        const savedRole =
          mode === 'create' ? await createRole(payload) : role ? await updateRole(role.id, payload) : null;

        if (!savedRole) {
          return;
        }

        if (formValues.picture) {
          const roleWithPicture = await uploadRolePicture(savedRole.id, formValues.picture);
          if (!roleWithPicture) {
            return;
          }
        }

        router.push(paths.adminRoles);
      },
    });

  const showFieldError = (field: keyof RoleFormValues) =>
    (touched[field] || submitCount > 0) && errors[field] ? (
      <Typography tag="p" variant="body-xs" textColor="error" className="mt-1">
        {errors[field]}
      </Typography>
    ) : null;

  return (
    <form
      onSubmit={handleSubmit}
      className={cn('grid w-full grid-cols-1 gap-5 lg:grid-cols-2 lg:gap-x-8 lg:gap-y-5', className)}
      noValidate
    >
      <div className="flex flex-col gap-5">
        <div>
          <TextInput
            label={t('fields.name')}
            name="name"
            value={values.name}
            onChange={handleChange}
            placeholder={t('fields.namePlaceholder')}
          />
          {showFieldError('name')}
        </div>

        <div>
          <TextareaInput
            label={t('fields.description')}
            name="description"
            value={values.description}
            onChange={handleChange}
            rows={4}
            placeholder={t('fields.descriptionPlaceholder')}
          />
          {showFieldError('description')}
        </div>

        <TextareaInput
          label={t('fields.ability')}
          name="ability"
          value={values.ability}
          onChange={handleChange}
          rows={3}
          placeholder={t('fields.abilityPlaceholder')}
        />
      </div>

      <div className="flex flex-col gap-5">
        <div className="flex flex-row items-start justify-between gap-4">
          <div className="min-w-0 flex-1">
            <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span" className="block">
              {values.isPlayable ? t('fields.rolePlayableStatus') : t('fields.roleUnplayableStatus')}
            </Typography>
            <Typography variant="body-sm" textColor="neutral-500" className="mt-1 block">
              {values.isPlayable ? t('fields.rolePlayableHint') : t('fields.roleUnplayableHint')}
            </Typography>
          </div>
          <SwitchInput
            variant="gradient"
            checked={values.isPlayable}
            className="shrink-0"
            onChange={(checked) => {
              void setFieldValue('isPlayable', checked, true);
              if (!checked) {
                void setFieldValue('type', '', false);
                setFieldError('type', undefined);
              }
            }}
            aria-label={values.isPlayable ? t('fields.rolePlayableStatus') : t('fields.roleUnplayableStatus')}
          />
        </div>

        {values.isPlayable && (
          <div>
            <SelectInput
              label={t('fields.type')}
              name="type"
              value={values.type}
              options={roleTypeOptions}
              onChange={handleChange}
            />
            {showFieldError('type')}
          </div>
        )}

        <MultiSelectInput
          label={t('fields.team')}
          name="teams"
          value={values.teams}
          options={teamOptions}
          tagVariant={(team) => gameTeamVariant(team as GameTeamEnum)}
          onChange={(teams) => {
            void setFieldValue('teams', teams as GameTeamEnum[]);
          }}
        />

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
          <TextInput
            label={t('fields.minPlayers')}
            name="minPlayers"
            type="number"
            min={0}
            value={values.minPlayers}
            onChange={handleChange}
            placeholder={t('fields.minPlayersPlaceholder')}
          />
          <TextInput
            label={t('fields.maxPerGame')}
            name="maxPerGame"
            type="number"
            min={0}
            value={values.maxPerGame}
            onChange={handleChange}
            placeholder={t('fields.maxPerGamePlaceholder')}
          />
        </div>

        <div className="flex flex-col gap-1">
          <Typography variant="body-sm" textColor="neutral-500" bold uppercase className="block">
            {t('fields.picture')}
          </Typography>
          {mode === 'update' && role?.picture && !values.picture && (
            <Image
              src={getImagePath(role.picture)}
              alt={role.name || ''}
              width={64}
              height={64}
              unoptimized
              className="relative! mb-2 h-12 w-12 rounded-sm object-cover"
            />
          )}
          <input
            type="file"
            name="picture"
            accept="image/*"
            onChange={(event) => {
              void setFieldValue('picture', event.currentTarget.files?.[0] ?? null);
            }}
            className="block w-full cursor-pointer rounded-sm border border-primary/15 bg-foreground/5 px-3 py-2 text-sm text-light file:mr-3 file:cursor-pointer file:rounded-sm file:border-0 file:bg-primary/20 file:px-3 file:py-1.5 file:text-sm file:text-primary"
          />
          <Typography tag="p" variant="body-xs" textColor="neutral-500">
            {values.picture ? values.picture.name : t('fields.pictureHint')}
          </Typography>
        </div>
      </div>

      <div className="lg:col-span-2">
        <Button
          type="submit"
          variant="accent"
          label={t('submit')}
          className="w-full sm:w-auto sm:min-w-48"
          loading={isSubmitting}
          disabled={isSubmitting}
        />
      </div>
    </form>
  );
};

export default RoleAdminForm;
