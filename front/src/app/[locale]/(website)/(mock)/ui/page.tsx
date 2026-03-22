import Typography from '@/components/ui/atoms/typography';
import Buttons from '@/components/ui/mock/ui-components/buttons';
import Colors from '@/components/ui/mock/ui-components/colors';
import Dividers from '@/components/ui/mock/ui-components/dividers';
import Gradients from '@/components/ui/mock/ui-components/gradients';
import ProgressBars from '@/components/ui/mock/ui-components/progress-bars';
import Tags from '@/components/ui/mock/ui-components/tags';
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
      <Dividers />
      <ProgressBars />
      <Tags />
    </main>
  );
};

export default UiPage;
