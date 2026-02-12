'use client'

import Typography from "@/components/ui/atoms/typography"
import Colors from "./ui-components/colors"
import Gradients from "./ui-components/gradients"
import Typographies from "./ui-components/typographies"
import Buttons from "./ui-components/buttons"

const UiClient = () => {
  return <main className="p-8">
    <Typography tag="h1" variant="heading-1" bold center className="block">Page UI</Typography>

    <Colors />
    <Gradients />
    <Typographies />
    <Buttons />
  </main>
};

export default UiClient;
