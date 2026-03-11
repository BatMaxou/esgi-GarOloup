'use client';

import Link from 'next/link';
import Image from 'next/image';
import dynamic from 'next/dynamic';

import Typography from '@/components/ui/atoms/typography';

const ThemeSwitcher = dynamic(() => import('@/components/layout/theme-switcher'), { ssr: false });

const Navbar = () => {
  return (
    <nav className="border-b-2 border-primary p-4 flex items-center justify-between sticky top-0 bg-background/90 backdrop-blur-sm">
      <Link href="/" className="flex items-center gap-4">
        <Image src="/logo.svg" alt="Logo" width={64} height={64} />
        <Typography variant="heading-2" special className="text-shadow-sm text-shadow-(color:--color-primary)">
          GarOloup
        </Typography>
      </Link>
      <ThemeSwitcher />
    </nav>
  );
};

export default Navbar;
