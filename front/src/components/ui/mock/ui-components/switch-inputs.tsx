'use client';

import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import SwitchInput from '@/components/ui/molecules/switch-input';

const SwitchInputs = () => {
  return (
    <div className="flex flex-col gap-8">
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Switch Inputs
      </Typography>

      <GlassPanel className="flex flex-col gap-6 p-8 w-fit">
        <Typography variant="subtitle" textColor="neutral-300">
          Checked
        </Typography>
        <ul className="flex flex-wrap gap-6 items-center">
          <li>
            <SwitchInput variant="primary" defaultChecked onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="secondary" defaultChecked onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="accent" defaultChecked onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="neutral" defaultChecked onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="error" defaultChecked onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="success" defaultChecked onChange={console.log} />
          </li>
        </ul>

        <Typography variant="subtitle" textColor="neutral-300">
          Unchecked
        </Typography>
        <ul className="flex flex-wrap gap-6 items-center">
          <li>
            <SwitchInput variant="primary" onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="secondary" onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="accent" onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="neutral" onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="error" onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="success" onChange={console.log} />
          </li>
        </ul>

        <Typography variant="subtitle" textColor="neutral-300">
          Gradient
        </Typography>
        <ul className="flex flex-wrap gap-6 items-center">
          <li>
            <SwitchInput variant="gradient" defaultChecked onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="gradient" onChange={console.log} />
          </li>
        </ul>

        <Typography variant="subtitle" textColor="neutral-300">
          Day / Night
        </Typography>
        <ul className="flex flex-wrap gap-6 items-center">
          <li>
            <SwitchInput variant="day-night" checkedIcon="moon" uncheckedIcon="sun" onChange={console.log} />
          </li>
        </ul>

        <Typography variant="subtitle" textColor="neutral-300">
          With icons
        </Typography>
        <ul className="flex flex-wrap gap-6 items-center">
          <li>
            <SwitchInput
              variant="primary"
              defaultChecked
              checkedIcon="garoloup"
              checkedIconClassColor="primary"
              uncheckedIcon="garoloup"
              uncheckedIconClassColor="light"
              size="lg"
              onChange={console.log}
            />
          </li>
          <li>
            <SwitchInput
              variant="accent"
              checkedIcon="sun"
              checkedIconClassColor="light"
              uncheckedIcon="moon"
              uncheckedIconClassColor="dark"
              onChange={console.log}
            />
          </li>
          <li>
            <SwitchInput variant="error" uncheckedIcon="x" onChange={console.log} />
          </li>
        </ul>

        <Typography variant="subtitle" textColor="neutral-300">
          Sizes
        </Typography>
        <ul className="flex flex-wrap gap-6 items-center">
          <li>
            <SwitchInput size="lg" defaultChecked onChange={console.log} />
          </li>
          <li>
            <SwitchInput size="md" defaultChecked onChange={console.log} />
          </li>
          <li>
            <SwitchInput size="sm" defaultChecked onChange={console.log} />
          </li>
        </ul>

        <Typography variant="subtitle" textColor="neutral-300">
          Disabled
        </Typography>
        <ul className="flex flex-wrap gap-6 items-center">
          <li>
            <SwitchInput variant="primary" defaultChecked disabled onChange={console.log} />
          </li>
          <li>
            <SwitchInput variant="primary" disabled onChange={console.log} />
          </li>
        </ul>
      </GlassPanel>
    </div>
  );
};

export default SwitchInputs;
