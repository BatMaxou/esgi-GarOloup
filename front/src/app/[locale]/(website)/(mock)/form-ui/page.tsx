import Typography from '@/components/ui/atoms/typography';
import TextInputs from '@/components/ui/mock/ui-components/text-inputs';
import TextareaInputs from '@/components/ui/mock/ui-components/textarea-inputs';
import SelectInputs from '@/components/ui/mock/ui-components/select-inputs';
import NumberInputs from '@/components/ui/mock/ui-components/number-inputs';
import SwitchInputs from '@/components/ui/mock/ui-components/switch-inputs';
import OTPInputs from '@/components/ui/mock/ui-components/otp-inputs';

const FormUIPage = () => {
  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-2" bold center className="block">
        Page Form UI
      </Typography>

      <TextInputs />
      <SelectInputs />
      <TextareaInputs />

      <NumberInputs />
      <SwitchInputs />
      <OTPInputs />
    </main>
  );
};

export default FormUIPage;
