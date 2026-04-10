const isClientSide = typeof window !== 'undefined';

export const publicUrl = process.env.NEXT_PUBLIC_URL || '';
export const apiBaseUrl =
  (isClientSide ? process.env.NEXT_PUBLIC_API_BASE_URL : process.env.NEXT_PUBLIC_SSR_API_BASE_URL) || '';
export const ftpClientUrl = process.env.NEXT_PUBLIC_FTP_BASE_URL || '';
export const ftpSsrUrl = process.env.NEXT_PUBLIC_SSR_FTP_BASE_URL || '';
export const ftpUrl = (isClientSide ? ftpClientUrl : ftpSsrUrl) || '';
export const mercureUrl =
  (isClientSide ? process.env.NEXT_PUBLIC_MERCURE_URL : process.env.NEXT_PUBLIC_SSR_MERCURE_URL) || '';
