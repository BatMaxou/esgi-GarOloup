import TestClient from '@/components/pages/test/test-client';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { getApiClient } from '@/utils/server/clients';

const TestPage = async () => {
  const game = await (await getApiClient()).game.getCurrent();
  if (game instanceof ApiClientError) {
    return <div>Error</div>;
  }

  console.log('-------- SERVER SIDE ----------');
  console.log(game);
  console.log('------------------');

  return <TestClient game={game} />;
};

export default TestPage;
