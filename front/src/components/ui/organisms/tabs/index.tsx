'use client';

import { AnimatePresence, motion } from 'motion/react';
import { useTranslations } from 'next-intl';
import { ReactNode, useState } from 'react';

import { itemCva, tabsCva } from './cva';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Typography from '@/components/ui/atoms/typography';

type Tab = {
  label: string;
  component: ReactNode;
};

type TabAlign = 'left' | 'center' | 'right';

type Props = {
  tabs: Tab[];
  currentTab?: string;
  align?: TabAlign;
  translate?: boolean;
};

const Tabs = ({ currentTab, tabs, align = 'left', translate = false }: Props) => {
  const [current, setCurrent] = useState(currentTab || tabs[0].label);
  const t = useTranslations();

  return (
    <div className={tabsCva({ align })}>
      <GlassPanel className="w-fit p-1">
        {/*
          NOTE: put ::after styling to the parent, to ensure compatibitlity with browsers that don't support anchor positioning 
          @see globals.css utilitities declarations
        */}
        <nav className="[&_*]:after:bg-linear-(--primary-gradient) [&_*]:after:rounded-xs">
          <ul className="flex flex-wrap justify-center list-follow-anchor">
            {tabs.map((tab) => (
              <li
                key={tab.label}
                className={itemCva({ active: current === tab.label })}
                onClick={() => setCurrent(tab.label)}
              >
                <Typography variant="button" textColor="controlled" bold>
                  {translate ? t(tab.label) : tab.label}
                </Typography>
              </li>
            ))}
          </ul>
        </nav>
      </GlassPanel>
      <AnimatePresence mode="wait">
        {tabs.map(
          (tab) =>
            current === tab.label && (
              <motion.div
                key={tab.label}
                initial={{ opacity: 0, scale: 0, backdropFilter: 'blur(0px)' }}
                animate={{ opacity: 1, scale: 1, backdropFilter: 'blur(4px)' }}
                exit={{ opacity: 0, scale: 0, backdropFilter: 'blur(0px)' }}
                transition={{ duration: 0.2 }}
              >
                {tab.component}
              </motion.div>
            )
        )}
      </AnimatePresence>
    </div>
  );
};

export default Tabs;
