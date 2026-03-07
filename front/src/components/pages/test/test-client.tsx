'use client';

import { useEffect } from 'react';

import Typography from '@/components/ui/atoms/typography';

const TestClient = () => {
  useEffect(() => {
    console.log('-------- CLIENT SIDE ----------');
    console.log('------------------');
  }, []);

  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-1" bold center className="block">
        Page de test
      </Typography>
    </main>
  );
};

export default TestClient;
