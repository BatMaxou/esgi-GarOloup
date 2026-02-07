import Buttons from "@/components/pages/ui/buttons";
import Colors from "@/components/pages/ui/colors";
import Gradients from "@/components/pages/ui/gradients";
import Typographies from "@/components/pages/ui/typographies";
import Typography from "@/components/ui/atoms/typography";

const UiPage = () => {
  return <main className="p-8">
    <Typography tag="h1" variant="heading-1" bold center className="block">Page UI</Typography>

    <Colors />
    <Gradients />
    <Typographies />
    <Buttons />
  </main>
}

export default UiPage;
