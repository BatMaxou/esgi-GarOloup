import Typography from '@/components/ui/atoms/typography';
import TextInputs from '@/components/ui/mock/ui-components/text-inputs';
import TextareaInputs from '@/components/ui/mock/ui-components/textarea-inputs';
import SelectInputs from '@/components/ui/mock/ui-components/select-inputs';

const FormUIPage = () => {
  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-2" bold center className="block">
        Page Form UI
      </Typography>

      <div className="flex flex-row gap-8">
        <TextInputs />
        <SelectInputs />
      </div>
      <TextareaInputs />
    </main>
  );
};

export default FormUIPage;
