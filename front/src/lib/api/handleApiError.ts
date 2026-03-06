import { ApiClientError } from '@/lib/api/ApiClientError';

export const handleApiError = async (response: Response): Promise<Response> => {
  if (!response.ok) {
    const serverResponse = await response.json();
    throw new ApiClientError(response.status, serverResponse.message);
  }

  return response;
};
