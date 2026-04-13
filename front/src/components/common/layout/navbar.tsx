'use client';

import { useMemo } from 'react';
import { useTranslations } from 'next-intl';
import { cva } from 'class-variance-authority';
import Image from 'next/image';
import dynamic from 'next/dynamic';

import Button from '@/components/ui/molecules/button';
import { useAuth } from '@/contexts/auth-context';
import { Link, usePathname, useRouter } from '@/i18n/navigation';
import { paths } from '@/utils/paths';

const ThemeSwitcher = dynamic(() => import('@/components/common/layout/theme-switcher'), { ssr: false });
const NavbarAuthActions = dynamic(() => import('@/components/common/layout/navbar-auth-actions'), {
  ssr: false,
  loading: () => <span className="flex min-h-8 min-w-22 shrink-0" aria-hidden />,
});

const navbarCva = cva(
  'border-b border-primary px-4 py-2 flex items-center justify-between fixed top-0 left-0 right-0 backdrop-blur-sm z-24',
  {
    variants: {
      sticky: {
        true: 'sticky',
        false: 'fixed',
      },
    },
  }
);

const Navbar = () => {
  const pathname = usePathname();
  const isSticky = useMemo(() => pathname !== paths.home, [pathname]);
  const { user } = useAuth();
  const router = useRouter();
  const t = useTranslations('components.common.layout.navbar');

  const handleJoinGame = () => {
    if (user) {
      router.push(paths.lobby);
      return;
    } else {
      router.push(paths.login);
    }
  };
  return (
    <nav className={navbarCva({ sticky: isSticky })}>
      <Link href={paths.home} className="flex items-center">
        <Image src="/logo.svg" alt={t('logoAlt')} width={64} height={64} />
      </Link>

      <ul className="flex items-center gap-4 text-glow-primary">
        <li>
          <Button asLink variant="text" label={t('roles')} href={paths.roles} />
        </li>
        <li>
          <Button asLink variant="text" label={t('mockLinks.ui')} href={paths.ui} />
        </li>
        <li>
          <Button asLink variant="text" label={t('mockLinks.icons')} href={paths.icons} />
        </li>
        <li>
          <Button asLink variant="text" label={t('mockLinks.formUi')} href={paths.formUi} />
        </li>
      </ul>

      <ul className="hidden sm:flex items-center gap-4">
        <li>
          <Button variant="accent" size="sm" label={t('playNow')} onClick={handleJoinGame} />
        </li>
        <li className="flex min-h-8 min-w-22 items-center justify-end">
          <NavbarAuthActions />
        </li>
        <li>
          <ThemeSwitcher />
        </li>
      </ul>
    </nav>
  );
};

export default Navbar;
