import Link from 'next/link';
import Image from 'next/image';

const Navbar = () => {
  return (
    <nav className="border-b border-primary-2 p-4 flex items-center justify-between">
      <Link href="/">
        <Image src="/logo.svg" alt="Logo" width={64} height={64} />
      </Link>
    </nav>
  );
};

export default Navbar;
