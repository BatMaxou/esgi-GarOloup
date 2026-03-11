'use client';

import Link from 'next/link';
import Image from 'next/image';
import dynamic from 'next/dynamic';

const ThemeSwitcher = dynamic(() => import('@/components/common/layout/theme-switcher'), { ssr: false });

import { usePathname } from 'next/navigation';
import { cva } from 'class-variance-authority';
import { useMemo } from 'react';
import { paths } from '@/utils/paths';
import Button from '@/components/ui/molecules/button';

const navbarCva = cva(
  'border-b border-primary p-4 flex items-center justify-between fixed top-0 left-0 right-0 backdrop-blur-sm z-24',
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

  return (
    <nav className={navbarCva({ sticky: isSticky })}>
      <Link href={paths.home} className="flex items-center gap-4">
        <Image src="/logo.svg" alt="Logo" width={64} height={64} />
      </Link>

      <ul className="flex items-center gap-4 text-shadow-[0_0_4px] text-shadow-(color:--color-background)">
        <li>
          <Button asLink variant="text" label="UI" href={paths.ui} />
        </li>
        <li>
          <Button asLink variant="text" label="Icons" href={paths.icons} />
        </li>
      </ul>

      <ul className="hidden sm:flex items-center gap-4">
        <li>
          <Button variant="accent" label="Jouer maintenant" />
        </li>
        <li>
          <Button variant="secondary" label="Se connecter" />
        </li>
        <li>
          <ThemeSwitcher />
        </li>
      </ul>
    </nav>
  );
};

export default Navbar;
