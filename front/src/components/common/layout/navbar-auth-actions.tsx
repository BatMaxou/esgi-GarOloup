'use client';

import { useTranslations } from 'next-intl';
import { toast } from 'react-toastify';

import Button from '@/components/ui/molecules/button';
import { useAuth } from '@/contexts/auth-context';
import { paths } from '@/utils/paths';

const NavbarAuthActions = () => {
  const { user, logout } = useAuth();
  const t = useTranslations('components.common.layout.navbar');

  const handleLogout = async () => {
    await logout();
    toast.success(t('logoutSuccess'));
  };

  return user ? (
    <Button variant="error" size="sm" glass label={t('logout')} type="button" onClick={() => void handleLogout()} />
  ) : (
    <Button asLink variant="secondary" size="sm" label={t('login')} href={paths.login} />
  );
};

export default NavbarAuthActions;
