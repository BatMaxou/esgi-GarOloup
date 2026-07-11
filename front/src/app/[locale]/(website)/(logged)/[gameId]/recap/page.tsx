import { notFound } from 'next/navigation';

import RecapClient from '@/components/pages/recap/recap-client';
import { getApiClient } from '@/utils/server/clients';
import { ApiClientError } from '@/lib/api/ApiClientError';

type Props = {
  params: Promise<{ gameId: string }>;
};

const RecapPage = async ({ params }: Props) => {
  const { gameId } = await params;
  const apiClient = await getApiClient();

  const recap = await apiClient.recap.getByGame(gameId);
  if (recap instanceof ApiClientError) {
    return notFound();
  }

  return <RecapClient recap={recap} />;
};

export default RecapPage;
