import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import Card from '@/components/ui/molecules/card';
import { useGame } from '@/contexts/game-context';
import { useTranslations } from 'next-intl';
import { useState } from 'react';

const GameMasterChoiceForm = () => {
  const { game, setGameMaster } = useGame();
  const t = useTranslations('components.common.form.game.gameMasterChoice');
  const playerList =
    game?.players?.map((player) => ({
      id: player.id,
      name: player.user?.username ?? '',
    })) ?? [];
  const [selectedPlayerId, setSelectedPlayerId] = useState<string | null>(null);

  const handleSelectPlayer = (playerId: string) => {
    if (selectedPlayerId === playerId) {
      setSelectedPlayerId(null);
      return;
    }
    setSelectedPlayerId(playerId);
  };

  return (
    <form
      className="flex flex-col gap-6"
      method="post"
      onSubmit={(e) => {
        e.preventDefault();
        if (!selectedPlayerId) {
          return;
        }
        setGameMaster(selectedPlayerId);
      }}
    >
      <Typography variant="subtitle" bold textColor="light" className="text-center">
        {t('sectionTitle')}
      </Typography>
      <Typography variant="body" textColor="secondary" className="text-center">
        {t('sectionSubtitle')}
      </Typography>
      <div className="flex flex-col gap-2 pr-2 overflow-y-scroll scrollbar max-h-96">
        {playerList.map((player) => (
          <Card
            key={player.id}
            onClick={() => handleSelectPlayer(player.id)}
            isCurrentPlayer={selectedPlayerId === player.id}
            liftOnHover={false}
            className="cursor-pointer flex items-center! justify-center! border! border-primary/15! w-full"
            variant="player"
          >
            <Typography variant="body" textColor="light">
              {player.name}
            </Typography>
          </Card>
        ))}
      </div>
      <Button type="submit" variant="accent" className="w-full" disabled={!selectedPlayerId} label={t('submit')} />
    </form>
  );
};

export default GameMasterChoiceForm;
