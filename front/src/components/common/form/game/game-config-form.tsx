'use client';

import { useEffect, useState } from 'react';
import { useFormik } from 'formik';
import { AnimatePresence, motion } from 'motion/react';
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
import type { RoleEntry, RolePlayable } from '@/utils/types';
import SwitchInput from '@/components/ui/molecules/switch-input';
import Divider from '@/components/ui/atoms/divider';
import { minPlayersToLaunchGame } from '@/utils/tools';

type GameConfigFormValues = {
  composition: {
    roles: RoleEntry[];
  };
  withGameMaster: boolean;
  withRandomDispatch: boolean;
};

type TooMuchPlayersPayload = {
  currentRoles: RoleEntry[];
  playableRoleList: RolePlayable[];
  onCompositionChange: (roles: RoleEntry[]) => void;
};

const hasTooManyRolesWithGameMaster = (roles: RoleEntry[], playerCount: number) =>
  getTotalRolesCount(roles) === playerCount;

const GameConfigForm = ({ onTooMuchPlayers }: { onTooMuchPlayers: (payload: TooMuchPlayersPayload) => void }) => {
  const t = useTranslations('components.common.form.game.gameConfig');
  const { game, setConfiguration } = useGame();
  const { getAllRoles, playableRoleList, roleListLoading } = useRole();
  const playerCount = game?.players?.length ?? 0;
  const isThereMorePlayersThanMinRequested = playerCount > minPlayersToLaunchGame;
  const canConfigureGameMaster = isThereMorePlayersThanMinRequested && !game?.public;
  const [currentStep, setCurrentStep] = useState<1 | 2>(canConfigureGameMaster ? 1 : 2);

  const { values, handleSubmit, handleChange, setFieldValue } = useFormik<GameConfigFormValues>({
    initialValues: {
      composition: {
        roles: [],
      },
      withGameMaster: false,
      withRandomDispatch: game?.public ? true : isThereMorePlayersThanMinRequested && !game?.public ? false : true,
    },
    onSubmit: (formValues) => {
      if (formValues.withGameMaster && hasTooManyRolesWithGameMaster(formValues.composition.roles, playerCount)) {
        onTooMuchPlayers({
          currentRoles: formValues.composition.roles,
          playableRoleList,
          onCompositionChange: (roles) => setFieldValue('composition.roles', roles),
        });
        return;
      }

      // Vérification de l'absence d'un gameMaster
      if (!formValues.withGameMaster) {
        formValues.withRandomDispatch = true;
      }

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

  useEffect(() => {
    if (playableRoleList.length === 0) {
      getAllRoles();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    if (!canConfigureGameMaster && currentStep === 1) {
      setCurrentStep(2);
    }
  }, [canConfigureGameMaster, currentStep]);

  if (!game?.players) {
    return null;
  }

  const isCompositionComplete = values.withGameMaster
    ? getTotalRolesCount(values.composition.roles) === playerCount - 1
    : getTotalRolesCount(values.composition.roles) === playerCount;

  const handleCompositionChange = (roles: RoleEntry[]) => {
    setFieldValue('composition.roles', roles);
  };

  return roleListLoading ? (
    <div className="flex flex-col gap-4 min-h-32">
      <Loader className="animate-spin" />
    </div>
  ) : (
    <>
      <form
        className="flex flex-col gap-6"
        method="post"
        onSubmit={(e) => {
          e.preventDefault();
          if (currentStep === 1) {
            setCurrentStep(2);
            return;
          }
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
            {currentStep === 1 ? t('stepSettingsSubtitle') : t('stepRolesSubtitle')}
          </Typography>
        </div>

        <AnimatePresence mode="wait" initial={false}>
          {currentStep === 1 ? (
            <motion.div
              key="step-settings"
              className="flex flex-col gap-6"
              initial={{ opacity: 0, x: -16 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -16 }}
              transition={{ duration: 0.2 }}
            >
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
                  checked={values.withGameMaster}
                  className="shrink-0"
                  onChange={() => {
                    const withGameMaster = !values.withGameMaster;
                    handleChange({ target: { name: 'withGameMaster', value: withGameMaster } });
                    if (withGameMaster && hasTooManyRolesWithGameMaster(values.composition.roles, playerCount)) {
                      onTooMuchPlayers({
                        currentRoles: values.composition.roles,
                        playableRoleList,
                        onCompositionChange: handleCompositionChange,
                      });
                    }
                  }}
                  aria-label={t('withGameMasterAria')}
                />
              </div>

              <div
                className="grid transition-[grid-template-rows] duration-300 ease-out"
                style={{ gridTemplateRows: values.withGameMaster ? '1fr' : '0fr' }}
              >
                <div className="flex min-h-0 flex-col gap-6 overflow-hidden">
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
                      checked={values.withRandomDispatch}
                      className="shrink-0"
                      onChange={() =>
                        handleChange({ target: { name: 'withRandomDispatch', value: !values.withRandomDispatch } })
                      }
                      aria-label={t('withRandomDispatchAria')}
                    />
                  </div>
                </div>
              </div>
              <Button type={'button'} variant="accent" label={t('nextStep')} full onClick={() => setCurrentStep(2)} />
            </motion.div>
          ) : (
            <motion.div
              key="step-roles"
              className="flex flex-col gap-6"
              initial={{ opacity: 0, x: 16 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: 16 }}
              transition={{ duration: 0.2 }}
            >
              <div
                className={`grid grid-cols-3 gap-4 max-h-128 overflow-y-scroll ${playableRoleList.length > 6 ? 'scrollbar pr-2' : ''}`}
              >
                {playableRoleList.map((role) => (
                  <RoleCardCounter
                    key={role.id}
                    role={role}
                    compositionRoles={values.composition.roles}
                    playerCount={playerCount}
                    thereIsGameMaster={values.withGameMaster}
                    onCompositionChange={handleCompositionChange}
                  />
                ))}
              </div>
              <div className="flex gap-3">
                <div className="w-1/3">
                  <Button
                    type="button"
                    variant="secondary"
                    label={t('previousStep')}
                    full
                    onClick={() => setCurrentStep(1)}
                  />
                </div>
                <div className={currentStep === 2 ? 'w-2/3' : 'w-full'}>
                  <Button type={'submit'} variant="accent" label={t('submit')} full disabled={!isCompositionComplete} />
                </div>
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </form>
    </>
  );
};

export default GameConfigForm;
