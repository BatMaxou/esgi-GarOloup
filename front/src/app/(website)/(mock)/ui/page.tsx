import Typography from '@/components/ui/atoms/typography';
import Buttons from '@/components/ui/mock/ui-components/buttons';
import Colors from '@/components/ui/mock/ui-components/colors';
import Gradients from '@/components/ui/mock/ui-components/gradients';
import Typographies from '@/components/ui/mock/ui-components/typographies';

const UiPage = () => {
  return (
    <main className="p-8">
      <Typography tag="h1" variant="heading-2" bold center className="block">
        Page UI
      </Typography>

      <Colors />
      <Gradients />
      <Typographies />
      <Buttons />
    </main>
  );
};

export default UiPage;
