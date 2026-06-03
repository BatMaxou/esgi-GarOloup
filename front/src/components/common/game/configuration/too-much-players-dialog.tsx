import { useState } from 'react';
import Image from 'next/image';
import Typography from '@/components/ui/atoms/typography';
import Card, { RoleCardVariant } from '@/components/ui/molecules/card';
import Dialog from '@/components/ui/molecules/dialog';
import { GameTeamEnum } from '@/utils/enums';
import { getImagePath } from '@/utils/getImagePath';
import { RoleEntry, RolePlayable } from '@/utils/types';
import classNames from 'classnames';
import { decrementRoleInComposition } from './RoleCardCounter';
import Button from '@/components/ui/molecules/button';

type TooMuchPlayersDialogProps = {
  open: boolean;
  setOpen: (open: boolean) => void;
  playableRoleList: RolePlayable[];
  currentRoles: RoleEntry[];
  onCompositionChange: (roles: RoleEntry[]) => void;
};

const TooMuchPlayersDialog = ({
  open,
  setOpen,
  playableRoleList,
  currentRoles,
  onCompositionChange,
}: TooMuchPlayersDialogProps) => {
  const [selectedRoleToSubstract, setSelectedRoleToSubstract] = useState<RolePlayable>();
  const configuredRoleTypes = new Set(currentRoles.map((entry) => entry.role));
  const currentPlayableRoles = playableRoleList.filter((role) => configuredRoleTypes.has(role.type));
  const handleSelectRole = (role: RolePlayable) => {
    if (!role) {
      return;
    }
    setSelectedRoleToSubstract(role);
  };

  const handleDecrement = (role: RolePlayable | undefined) => {
    if (!role) {
      return;
    }
    const nextRoles = decrementRoleInComposition(currentRoles, role.type);
    if (nextRoles) {
      onCompositionChange(nextRoles);
    }
  };

  const handleDecrementAndClose = () => {
    handleDecrement(selectedRoleToSubstract);
    setOpen(false);
  };

  return (
    <Dialog
      open={open}
      setOpen={setOpen}
      isClosable={false}
      size="md"
      title="Nombre de joueurs maximum atteint"
      description="Vous devez retirer un rôle pour confirmer la configuration de votre partie."
    >
      <Typography variant="body-sm" bold textColor="light">
        Le maître du jeu comptant comme un rôle à part entière, vous devez choisir quel rôle se voit être réduit.
      </Typography>

      <div className="flex flex-row justify-start items-start gap-4">
        {currentPlayableRoles.map((role: RolePlayable) => (
          <div
            key={role.id}
            className={classNames(
              selectedRoleToSubstract?.type === role.type
                ? 'border-2 border-primary rounded-sm'
                : 'border-2 border-transparent'
            )}
          >
            <Card
              type="role"
              variant={(role.teams?.[0] ?? GameTeamEnum.VILLAGE) as RoleCardVariant}
              className="overflow-hidden max-h-48 aspect-3/4 w-36 cursor-pointer"
              fullfilled
              liftOnHover={false}
              onClick={() => {
                handleSelectRole(role);
              }}
            >
              <div className="relative aspect-3/4 min-h-48 w-36">
                {role.picture ? (
                  <Image
                    src={getImagePath(role.picture)}
                    alt={role.name ?? ''}
                    fill
                    unoptimized
                    className="object-cover"
                  />
                ) : (
                  <div className="absolute inset-0 bg-dark/60" />
                )}
                <div className="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/80 via-black/40 to-transparent px-2 py-3">
                  <Typography variant="body-sm" bold className="text-glow-dark text-center">
                    {role.name}
                  </Typography>
                </div>
              </div>
            </Card>
          </div>
        ))}
      </div>
      <Button variant="primary" onClick={() => handleDecrementAndClose()} label="Retirer le rôle" />
    </Dialog>
  );
};

export default TooMuchPlayersDialog;
