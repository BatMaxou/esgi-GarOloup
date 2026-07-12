'use client';

import { useEffect } from 'react';
import { useParams } from 'next/navigation';
import { useTranslations } from 'next-intl';
import { ArrowLeft } from 'lucide-react';

import RoleUpdateForm from '@/components/common/form/admin/role-update-form';
import Button from '@/components/ui/molecules/button';
import TextSkeleton from '@/components/ui/atoms/skeleton';
import Typography from '@/components/ui/atoms/typography';
import { useRole } from '@/contexts/role-context';
import { paths } from '@/utils/paths';

const AdminRolesEditClient = () => {
  const t = useTranslations('components.pages.admin.rolesUpdate');
  const params = useParams<{ roleId: string }>();
  const roleId = params.roleId;
  const { role, roleLoading, getRole } = useRole();

  useEffect(() => {
    if (roleId) {
      getRole({ ref: roleId });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [roleId]);

  const editingRole = role?.id === roleId ? role : null;

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

      {roleLoading ? (
        <div className="flex flex-col gap-3">
          <TextSkeleton className="h-12 w-full" />
          <TextSkeleton className="h-32 w-full" />
          <TextSkeleton className="h-32 w-full" />
        </div>
      ) : editingRole ? (
        <RoleUpdateForm role={editingRole} />
      ) : (
        <Typography variant="body-sm" textColor="neutral-500" className="py-8 text-center">
          {t('notFound')}
        </Typography>
      )}
    </main>
  );
};

export default AdminRolesEditClient;
