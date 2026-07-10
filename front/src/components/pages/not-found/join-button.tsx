'use client';

import Button from '@/components/ui/molecules/button';
import { useAuth } from '@/contexts/auth-context';
import { useRouter } from '@/i18n/navigation';
import { paths } from '@/utils/paths';

type Props = {
  label: string;
};

const JoinButton = ({ label }: Props) => {
  const { user } = useAuth();
  const router = useRouter();

  const handleClick = () => {
    router.push(user ? paths.lobby : paths.login);
  };

  return (
    <Button variant="secondary" glass size="lg" popup onClick={handleClick} label={label} className="w-full sm:w-fit" />
  );
};

export default JoinButton;
