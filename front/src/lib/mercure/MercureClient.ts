import { ApiClient } from '@/lib/api/ApiClient';
import { ClientCookieRegistry } from '@/lib/cookie/ClientCookieRegistry';
import { Game, Player } from '@/utils/types';
import { topics } from './topics';
import { ApiClientError } from '../api/ApiClientError';

export class MercureClient {
  public token: string | null = null;
  private COOKIE_NAME = 'mercureAuthorization';

  constructor(
    public baseUrl: string,
    private readonly apiClient: ApiClient,
    private readonly cookieRegistry: ClientCookieRegistry,
  ) {}

  public subscribe<T>(topic: string, onMessage: ({ data }: { data: T }) => void): EventSource | null {
    if (!this.token) {
      return null;
    }

    const url = new URL(this.baseUrl);
    url.searchParams.append('topic', topic);

    const eventSource = new EventSource(url, { withCredentials: true });
    eventSource.onopen = () => console.log('SSE connection opened.');
    eventSource.onmessage = onMessage;

    return eventSource;
  }

  public watchGame(id: string, action: (game: Game) => void): EventSource | null {
    return this.subscribe(topics.game(id), ({ data }: { data: string }) => action(JSON.parse(data)));
  }

  public watchPlayer(id: string, action: (player: Player) => void): EventSource | null {
    return this.subscribe(topics.player(id), ({ data }: { data: string }) => action(JSON.parse(data)));
  }

  public async fetchCredentials(): Promise<void> {
    const response = await this.apiClient.mercure.getToken();
    if (response instanceof ApiClientError || !response.token) {
      this.cookieRegistry.eraseCookie(this.COOKIE_NAME);
      this.token = null;

      return;
    }

    this.cookieRegistry.setCookie(this.COOKIE_NAME, response.token);
    this.token = response.token;
  }
}
