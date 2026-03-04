export class ApiClientError extends Error {
  public constructor(
    public code: number,
    message: string
  ) {
    super(message);

    this.name = 'ApiClientError';
  }
}
