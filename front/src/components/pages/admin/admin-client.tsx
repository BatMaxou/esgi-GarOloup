'use client';

import { useTranslations } from 'next-intl';

import Card from '@/components/ui/molecules/card';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { paths } from '@/utils/paths';

const AdminClient = () => {
  const t = useTranslations('components.pages.admin');

  return (
    <main className="mx-auto w-full max-w-[1100px] px-6 py-[100px]">
      <div className="mb-10">
        <Typography tag="div" textColor="primary" bold>
          {t('eyebrow')}
        </Typography>
        <Typography tag="h1" variant="heading-2" bold>
          {t('title')}
        </Typography>
        <Typography tag="p" className="mt-2">
          {t('subtitle')}
        </Typography>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <Card
          href={paths.adminRoles}
          variant="accent"
          orientation="horizontal"
          className="items-center gap-4 px-5 py-5"
        >
          <Icon name="users" className="h-10 w-10 shrink-0 text-primary" />
          <div className="flex min-w-0 flex-col gap-1">
            <Typography tag="h2" variant="subtitle" bold>
              {t('sections.roles.title')}
            </Typography>
            <Typography tag="p" variant="body-sm" textColor="neutral-200">
              {t('sections.roles.description')}
            </Typography>
          </div>
          <Icon name="arrow-right" className="ml-auto h-5 w-5 shrink-0 text-primary" />
        </Card>
      </div>
    </main>
  );
};

export default AdminClient;
