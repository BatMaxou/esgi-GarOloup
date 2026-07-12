'use client';

import { useEffect } from 'react';
import { useTranslations } from 'next-intl';
import { toast } from 'react-toastify';

import { useRouter } from '@/i18n/navigation';
import { paths } from '@/utils/paths';

const AdminForbiddenRedirect = () => {
  const router = useRouter();
  const t = useTranslations('layouts.admin');

  useEffect(() => {
    toast.error(t('forbidden'));
    router.replace(paths.home);
  }, [router, t]);

  return null;
};

export default AdminForbiddenRedirect;
