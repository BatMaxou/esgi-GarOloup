'use client'

import { useEffect } from "react";

import Buttons from "@/components/pages/ui/buttons";
import Colors from "@/components/pages/ui/colors";
import Gradients from "@/components/pages/ui/gradients";
import Typographies from "@/components/pages/ui/typographies";
import Typography from "@/components/ui/atoms/typography";

const UiPage = () => {
  useEffect(() => {
    const url = new URL('http://localhost:8888/.well-known/mercure');
    url.searchParams.append('topic', 'http://localhost:8000/api/games/3');

    const eventSource = new EventSource(url);

    // The callback will be called every time an update is published
    eventSource.onmessage = function ({data}) {
        console.log(data);
    };
  }, []);

  return <main className="p-8">
    <Typography tag="h1" variant="heading-1" bold center className="block">Page UI</Typography>

    <Colors />
    <Gradients />
    <Typographies />
    <Buttons />
  </main>
}

export default UiPage;
