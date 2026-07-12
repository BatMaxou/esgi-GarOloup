import type { useTranslations } from 'next-intl';

import type { IconName } from '@/components/ui/atoms/icon/config';

export type Translate = ReturnType<typeof useTranslations>;

export type EventColor = 'red' | 'green' | 'blue' | 'pink' | 'yellow' | 'neutral';

export type JournalEvent = {
  id: string;
  icon: IconName;
  color: EventColor;
  text: string;
};
