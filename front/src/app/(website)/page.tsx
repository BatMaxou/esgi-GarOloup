import Typography from '@/components/ui/atoms/typography';
import Link from 'next/link';

const HomePage = () => {
  return (
    <>
      <Typography variant="heading-1" bold>
        <Link href="/game">GAME</Link>
      </Typography>
    </>
  );
};

export default HomePage;
