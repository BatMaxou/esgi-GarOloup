'use client';

import Hero from '@/components/common/home/hero';
import Roles from '@/components/common/home/roles';
import Rules from '@/components/common/home/rules';
import WhyUs from '@/components/common/home/whyUs';
import FastJoin from '@/components/common/home/fastJoin';
import Faq from '@/components/common/home/faq';
import Evolutions from '@/components/common/home/evolutions';
import Divider from '@/components/ui/atoms/divider';

const HomeClient = () => {
  return (
    <main>
      <Hero />
      <Divider variant="secondary" className="w-full" />
      <Roles />
      <Divider variant="secondary" className="w-full" />
      <Rules />
      <Divider variant="secondary" className="w-full" />
      <WhyUs />
      <Divider variant="secondary" className="w-full" />
      <FastJoin />
      <Divider variant="secondary" className="w-full" />
      <Faq />
      <Divider variant="secondary" className="w-full" />
      <Evolutions />
    </main>
  );
};

export default HomeClient;
