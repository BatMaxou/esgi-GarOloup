'use client';

import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import TabsComponent from '@/components/ui/organisms/tabs';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Divider from '../../atoms/divider';

const Tab = ({ letter }: { letter: string }) => {
  return (
    <GlassPanel className="flex flex-col gap-6 p-8">
      <Typography tag="p" variant="body" className="text-neutral-400">
        Content {letter}
      </Typography>
      <div className="flex flex-wrap justify-end gap-2">
        <Button variant="secondary" type="button" label="Annuler" />
        <Button variant="accent" type="button" label="Confirmer" />
      </div>
    </GlassPanel>
  );
};

const Tabs = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Tabs
      </Typography>
      <ul className="flex flex-col gap-6">
        <li>
          <TabsComponent
            tabs={[
              { label: 'Tab A', component: <Tab letter="A" /> },
              { label: 'Tab B', component: <Tab letter="B" /> },
              { label: 'Tab C', component: <Tab letter="C" /> },
            ]}
          />
        </li>
        <li>
          <Divider />
        </li>
        <li>
          <TabsComponent
            align="center"
            tabs={[
              { label: 'Tab A', component: <Tab letter="A" /> },
              { label: 'Tab B', component: <Tab letter="B" /> },
              { label: 'Tab C', component: <Tab letter="C" /> },
            ]}
          />
        </li>
        <li>
          <Divider />
        </li>
        <li>
          <TabsComponent
            align="right"
            tabs={[
              { label: 'Tab A', component: <Tab letter="A" /> },
              { label: 'Tab B', component: <Tab letter="B" /> },
              { label: 'Tab C', component: <Tab letter="C" /> },
            ]}
          />
        </li>
      </ul>
    </>
  );
};

export default Tabs;
