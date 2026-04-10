import type { NextConfig } from 'next';
import createNextIntlPlugin from 'next-intl/plugin';

import { ftpClientUrl, ftpSsrUrl } from '@/utils/tools';

const ftpUrls = [ftpClientUrl, ftpSsrUrl].filter(Boolean);

const nextConfig: NextConfig = {
  /* config options here */
  output: 'standalone',
  images: {
    remotePatterns: ftpUrls.map((raw: string) => {
      const { protocol, hostname, port } = new URL(raw);

      return {
        protocol: protocol.replace(':', '') as 'http' | 'https',
        hostname,
        ...(port ? { port } : {}),
      };
    }),
  },
  turbopack: {
    rules: {
      '*.svg': {
        loaders: ['@svgr/webpack'],
        as: '*.js',
      },
    },
  },
};

const withNextIntl = createNextIntlPlugin();

export default withNextIntl(nextConfig);
