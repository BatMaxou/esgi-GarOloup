import { defineRouting } from 'next-intl/routing';

import { pathnames } from './pathnames';

export const routing = defineRouting({
  locales: ['fr', 'en'],
  defaultLocale: 'fr',
  localePrefix: 'as-needed',
  pathnames,
});
