import { useEffect, useMemo } from 'react';
import { useFormik } from 'formik';
import { useTranslations } from 'next-intl';
import Typography from '@/components/ui/atoms/typography';
import { useGame } from '@/contexts/game-context';
import { useRole } from '@/contexts/role-context';
import { Loader } from 'lucide-react';
import { GameRoleEnum } from '@/utils/enums';
import { Player, RoleDispatchEntry } from '@/utils/types';
import SelectInput from '@/components/ui/molecules/select-input';
import Divider from '@/components/ui/atoms/divider';
import Button from '@/components/ui/molecules/button';

type GameRoleDispatchFormValues = {
  dispatch: Record<string, GameRoleEnum | ''>;
};
type RoleCountMap = Partial<Record<GameRoleEnum, number>>;

const getRoleTypeFromEntry = (roleEntry: unknown): GameRoleEnum | null => {
  if (!roleEntry) {
    return null;
  }

  if (typeof roleEntry === 'string') {
    return roleEntry as GameRoleEnum;
  }

  if (typeof roleEntry === 'object' && 'type' in roleEntry && typeof roleEntry.type === 'string') {
    return roleEntry.type as GameRoleEnum;
  }

  return null;
};

const GameRoleDispatchForm = () => {
  const t = useTranslations('components.common.form.game.gameRoleDispatch');
  const { game, dispatchRoles } = useGame();
  const { getAllRoles, playableRoleList, roleListLoading } = useRole();

  useEffect(() => {
    if (playableRoleList.length === 0) {
      getAllRoles();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const playerList = useMemo<Player[]>(
    () =>
      game?.players?.map((player) => ({
        id: player.id,
        username: player.user?.username ?? '',
      })) ?? [],
    [game?.players]
  );

  // Retourne la limite pour chaque role
  const roleLimits = useMemo(
    () =>
      (game?.configuration?.composition?.roles ?? []).reduce<RoleCountMap>((acc, entry) => {
        const roleType = getRoleTypeFromEntry(entry.role);
        if (!roleType || !entry.count) {
          return acc;
        }
        acc[roleType] = entry.count;
        return acc;
      }, {}),
    [game?.configuration?.composition?.roles]
  );

  // Dispatch initial pour Formik
  const initialDispatch = useMemo(
    () => Object.fromEntries(playerList.map((player) => [player.id, ''])) as Record<string, GameRoleEnum | ''>,
    [playerList]
  );

  // Formik
  const { handleSubmit, values, setFieldValue } = useFormik<GameRoleDispatchFormValues>({
    initialValues: {
      dispatch: initialDispatch,
    },
    onSubmit: (values) => {
      const payload = Object.entries(values.dispatch).reduce<RoleDispatchEntry[]>((acc, [playerId, role]) => {
        if (!role) {
          return acc;
        }
        acc.push({ playerId, role });
        return acc;
      }, []);

      dispatchRoles(payload);
    },
  });

  // Calcul du nombre de fois que chaque rôle a été assigné
  const roleUsageCount = useMemo(
    () =>
      Object.values(values.dispatch).reduce<RoleCountMap>((acc, selectedRole) => {
        if (!selectedRole) {
          return acc;
        }

        acc[selectedRole] = (acc[selectedRole] ?? 0) + 1;
        return acc;
      }, {}),
    [values.dispatch]
  );

  // Vérification si un rôle spécifique peut être assigné à un joueur
  const canAssignRole = (playerId: string, roleType: GameRoleEnum) => {
    const limit = roleLimits[roleType] ?? 0;
    if (limit <= 0) {
      return false;
    }

    const currentRole = values.dispatch[playerId];
    if (currentRole === roleType) {
      return true;
    }

    return (roleUsageCount[roleType] ?? 0) < limit;
    
  };

  // Génération des options possibles pour un joueur
  const getAllowedOptionsForPlayer = (playerId: string) =>
    playableRoleList
      .filter((role) => canAssignRole(playerId, role.type))
      .map((role) => ({
        label: role.name,
        value: role.type,
      }));

  const isDispatchComplete = playerList.every((player) => Boolean(values.dispatch[player.id]));
  
  if (roleListLoading) {
    return <Loader />;
  }
  
  return (
    <form className="flex flex-col gap-6" onSubmit={handleSubmit}>
      <Typography variant="subtitle" bold textColor="light" className="text-center">
        {t('sectionTitle')}
      </Typography>
      <Typography variant="body" textColor="secondary" className="text-center">
        {t('sectionSubtitle')}
      </Typography>

      <div className="flex flex-col gap-2 pr-2 overflow-y-scroll scrollbar max-h-96">
        {playerList.map((player: Player) => (
          <div key={player.id} className="flex flex-col gap-2">
            <div className="flex flex-row items-center justify-between gap-6 py-1">
              <Typography variant="body" textColor="light">
                {player.username}
              </Typography>
              <div className="flex items-center gap-2 w-full min-w-42 max-w-48">
                <SelectInput
                  name={`dispatch.${player.id}`}
                  value={values.dispatch[player.id] ?? ''}
                  onChange={(event) => setFieldValue(`dispatch.${player.id}`, event.target.value)}
                  options={[
                    { label: '-', value: '' },
                    ...getAllowedOptionsForPlayer(player.id),
                  ]}
                />
                <Button
                  variant="text"
                  className="cursor-pointer text-neutral-400 hover:text-primary w-2 h-2 transition-colors"
                  onClick={() => setFieldValue(`dispatch.${player.id}`, '')}
                  disabled={!values.dispatch[player.id]}
                  leftIcon="crown"
                />
              </div>
            </div>
            <Divider variant="primary" />
          </div>
        ))}
      </div>
      <Button type="submit" variant="accent" className="w-full" disabled={!isDispatchComplete} label={t('submit')} />
    </form>
  );
};

export default GameRoleDispatchForm;