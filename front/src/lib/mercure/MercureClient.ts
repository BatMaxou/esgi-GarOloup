import { topics } from "./topics";

export class MercureClient {
  baseUrl: string;
  baseApiUrl: string;

  constructor(mercureUrl: string, apiBaseUrl: string) {
    this.baseUrl = mercureUrl;
    this.baseApiUrl = apiBaseUrl;
  }

  public async subscribe<T>(topic: string, onMessage: ({ data }: { data: T }) => void): Promise<void> {
    console.log(this.baseUrl);
    const url = new URL(this.baseUrl);
    url.searchParams.append('topic', topic);

    const eventSource = new EventSource(url);
    eventSource.onopen = () => console.log('SSE connection opened.');
    eventSource.onmessage = onMessage;
  }

  public watchGame(id: number, action: (game: { step: string }) => void): void {
    this.subscribe(`${this.baseApiUrl}${topics.game(id)}`, ({ data }: { data: string }) => {
      const retrieve = JSON.parse(data);

      action(retrieve);
    });
  }
}

