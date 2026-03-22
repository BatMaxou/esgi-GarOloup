import Typography from '@/components/ui/atoms/typography';
import Icons from '@/components/ui/mock/ui-components/icons';

const IconsPage = () => {
  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-2" bold center className="block">
        Page Icons
      </Typography>

      <Icons />
    </main>
  );
};

export default IconsPage;
