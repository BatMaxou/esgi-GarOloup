import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import ClickAndCopy from '@/components/ui/organisms/click-and-copy';

const GameJoinCode = ({ joinCode }: { joinCode: string }) => {
  return (
    <Card variant={'accent'} className="px-4! py-2!" liftOnHover={false}>
      <Typography tag="p" variant="body-xs" bold uppercase center textColor="accent" className="text-glow-accent">
        Code :
      </Typography>
      <ClickAndCopy valueToCopy={joinCode} iconClassName="text-accent text-glow-accent" iconPosition="left">
        <Typography tag="span" variant="body-xs" bold textColor="accent" className="text-glow-accent">
          {joinCode}
        </Typography>
      </ClickAndCopy>
    </Card>
  );
};

export default GameJoinCode;
