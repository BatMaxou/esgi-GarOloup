'use client';

import { useTranslations } from 'next-intl';
import { ArrowLeft } from 'lucide-react';

import RoleCreateForm from '@/components/common/form/admin/role-create-form';
import Button from '@/components/ui/molecules/button';
import Typography from '@/components/ui/atoms/typography';
import { paths } from '@/utils/paths';

const AdminRolesCreateClient = () => {
  const t = useTranslations('components.pages.admin.rolesCreate');

  return (
    <main className="mx-auto w-full max-w-[1100px] px-6 py-[100px]">
      <div className="mb-8 flex flex-col gap-4">
        <div className="mb-2 flex items-center gap-2">
          <ArrowLeft className="size-4" />
          <Button variant="text" size="sm" label={t('backToRoles')} asLink href={paths.adminRoles} />
        </div>
        <Typography tag="div" textColor="primary" bold>
          {t('eyebrow')}
        </Typography>
        <Typography tag="h1" variant="heading-2" bold>
          {t('title')}
        </Typography>
        <Typography tag="p">{t('subtitle')}</Typography>
      </div>

      <RoleCreateForm />
    </main>
  );
};

export default AdminRolesCreateClient;
