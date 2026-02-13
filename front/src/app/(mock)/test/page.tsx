import TestClient from "@/components/pages/test/test-client";
import { ApiClientError } from "@/lib/api/ApiClientError";
import { getApiClient } from "@/utils/server/clients";

const TestPage = async () => {
  const game = await (await getApiClient()).game.get('019c58aa-7230-7239-a8dc-62af8d19d9c9');
  if (game instanceof ApiClientError) {
    return <div>Error</div>
  }

  console.log('-------- SERVER SIDE ----------')
  console.log(game);
  console.log('------------------')

  return <TestClient game={game} />
}

export default TestPage;
