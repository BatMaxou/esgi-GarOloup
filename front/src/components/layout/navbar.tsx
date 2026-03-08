import Link from 'next/link';
import Image from 'next/image';
import ThemeSwitcher from './theme-switcher';

const Navbar = () => {
  return (
    <nav className="border-b-2 border-primary-2 p-4 flex items-center justify-between sticky top-0 bg-background/90">
      <Link href="/">
        <Image src="/logo.svg" alt="Logo" width={64} height={64} />
      </Link>
      <ThemeSwitcher />
    </nav>
  );
};

export default Navbar;
