'use client';

import JoinGameForm from '@/components/common/form/lobby/join-game-form';
import Card from '@/components/ui/molecules/card';

const JoinGame = () => {
  return (
    <Card className="w-full p-10" orientation="vertical" hoverable={false}>
      <JoinGameForm />
    </Card>
  );
};

export default JoinGame;
