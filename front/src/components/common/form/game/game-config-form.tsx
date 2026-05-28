'use client';

import { useEffect } from 'react';
import { useFormik } from 'formik';
import { useTranslations } from 'next-intl';
import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import { useRole } from '@/contexts/role-context';
import { GameConfigurationPayload } from '@/lib/api/resources/GameResource';
import { Loader } from 'lucide-react';
import { GameRoleEnum } from '@/utils/enums';
import { useGame } from '@/contexts/game-context';
import RoleCardCounter from '../../game/configuration/RoleCardCounter';
import { getTotalRolesCount } from '../../game/configuration/RoleCardCounter';
import type { RoleEntry } from '@/utils/types';
import SwitchInput from '@/components/ui/molecules/switch-input';
import Divider from '@/components/ui/atoms/divider';

type GameConfigFormValues = {
  composition: {
    roles: RoleEntry[];
  };
  withGameMaster: boolean;
  withRandomDispatch: boolean;
};

const GameConfigForm = () => {
  const t = useTranslations('components.common.form.game.gameConfig');
  const { game, setConfiguration } = useGame();
  const { values, handleSubmit, handleChange, setFieldValue } = useFormik<GameConfigFormValues>({
    initialValues: {
      composition: {
        roles: [],
      },
      withGameMaster: false,
      withRandomDispatch: false,
    },
    onSubmit: (formValues) => {
      const payload: GameConfigurationPayload = {
        ...formValues,
        composition: {
          roles: formValues.composition.roles
            .filter((entry): entry is RoleEntry & { role: GameRoleEnum; count: number } =>
              Boolean(entry.role && entry.count)
            )
            .map((entry) => ({ role: entry.role, count: entry.count })),
        },
      };
      setConfiguration(payload);
    },
  });

  const { getAllRoles, playableRoleList, roleListLoading } = useRole();

  useEffect(() => {
    if (playableRoleList.length === 0) {
      getAllRoles();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  if (!game?.players) {
    return null;
  }

  const playerCount = game.players.length;
  const isCompositionComplete = getTotalRolesCount(values.composition.roles) === playerCount;

  const handleCompositionChange = (roles: RoleEntry[]) => {
    setFieldValue('composition.roles', roles);
  };

  return roleListLoading ? (
    <div className="flex flex-col gap-4 min-h-32">
      <Loader className="animate-spin" />
    </div>
  ) : (
    <form
      className="flex flex-col gap-6"
      method="post"
      onSubmit={(e) => {
        e.preventDefault();
        if (!isCompositionComplete) {
          return;
        }
        handleSubmit();
      }}
    >
      <div className="flex flex-col gap-2">
        <Typography variant="subtitle" bold textColor="light" className="text-center">
          {t('sectionTitle')}
        </Typography>
        <Typography variant="body" textColor="secondary" className="text-center">
          {t('sectionSubtitle')}
        </Typography>
      </div>

      <div className="grid grid-cols-3 gap-4">
        {playableRoleList.map((role) => (
          <RoleCardCounter
            key={role.id}
            role={role}
            compositionRoles={values.composition.roles}
            playerCount={playerCount}
            onCompositionChange={handleCompositionChange}
          />
        ))}
      </div>

      <Divider variant="primary" />

      <div className="flex flex-row items-start justify-between gap-4">
        <div className="min-w-0 flex-1">
          <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span" className="block">
            {t('withGameMasterTitle')}
          </Typography>
          <Typography variant="body-sm" textColor="neutral-500" className="mt-1 block">
            {t('withGameMasterLabel')}
          </Typography>
        </div>
        <SwitchInput
          variant="gradient"
          onChange={() => handleChange({ target: { name: 'withGameMaster', value: !values.withGameMaster } })}
          aria-label={t('withGameMasterAria')}
        />
      </div>

      <Divider variant="primary" />

      <div className="flex flex-row items-start justify-between gap-4">
        <div className="min-w-0 flex-1">
          <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span" className="block">
            {t('withRandomDispatchTitle')}
          </Typography>
          <Typography variant="body-sm" textColor="neutral-500" className="mt-1 block">
            {t('withRandomDispatchLabel')}
          </Typography>
        </div>
        <SwitchInput
          variant="gradient"
          onChange={() => handleChange({ target: { name: 'withRandomDispatch', value: !values.withRandomDispatch } })}
          aria-label={t('withRandomDispatchAria')}
        />
      </div>

      <Button type="submit" variant="accent" label={t('submit')} full disabled={!isCompositionComplete} />
    </form>
  );
};

export default GameConfigForm;
