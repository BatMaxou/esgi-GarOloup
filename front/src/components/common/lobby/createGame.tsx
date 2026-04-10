'use client';

import CreateGameForm from '@/components/common/form/lobby/create-game-form';
import Card from '@/components/ui/molecules/card';

const CreateGame = () => {
  return (
    <Card className="w-full p-10" orientation="vertical" hoverable={false}>
      <CreateGameForm />
    </Card>
  );
};

export default CreateGame;
