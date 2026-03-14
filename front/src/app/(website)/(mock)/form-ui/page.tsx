import Typography from '@/components/ui/atoms/typography';
import TextInputs from '@/components/ui/mock/ui-components/text-inputs';

const FormUIPage = () => {
  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-2" bold center className="block">
        Page Form UI
      </Typography>

      <TextInputs />
    </main>
  );
};

export default FormUIPage;
