import { topics } from './topics';
import { Game } from '@/utils/types';

export class MercureClient {
  constructor(
    public baseUrl: string,
    public apiBaseUrl: string
  ) {}

  public async subscribe<T>(
    topic: string,
    onMessage: ({ data }: { data: T }) => void
  ): Promise<void> {
    const url = new URL(this.baseUrl);
    url.searchParams.append('topic', topic);

    const eventSource = new EventSource(url);
    eventSource.onopen = () => console.log('SSE connection opened.');
    eventSource.onmessage = onMessage;
  }

  public watchGame(id: string, action: (game: Game) => void): void {
    this.subscribe(
      `${this.apiBaseUrl}${topics.game(id)}`,
      ({ data }: { data: string }) => action(JSON.parse(data))
    );
  }
}
