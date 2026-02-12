import TestClient from "@/components/pages/test/test-client";
import { getApiClient } from "@/utils/server/clients";

const TestPage = async () => {
  const game = await (await getApiClient()).game.get(3);

  console.log('-------- SERVER SIDE ----------')
  console.log(game);
  console.log('------------------')

  return <TestClient game={game} />
}

export default TestPage;
