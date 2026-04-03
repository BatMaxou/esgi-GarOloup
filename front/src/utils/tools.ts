const isClientSide = typeof window !== 'undefined';

export const publicUrl = process.env.NEXT_PUBLIC_URL || '';
export const apiBaseUrl =
  (isClientSide ? process.env.NEXT_PUBLIC_API_BASE_URL : process.env.NEXT_PUBLIC_SSR_API_BASE_URL) || '';
export const ftpUrl = (isClientSide ? process.env.NEXT_PUBLIC_FTP_URL : process.env.NEXT_PUBLIC_SSR_FTP_URL) || '';
export const mercureUrl =
  (isClientSide ? process.env.NEXT_PUBLIC_MERCURE_URL : process.env.NEXT_PUBLIC_SSR_MERCURE_URL) || '';

const getImagePath = (path: string) => {
  const normalized = path.replace(/^\/+/, '');

  return `${ftpUrl}/${normalized}`;
};

export { getImagePath };
