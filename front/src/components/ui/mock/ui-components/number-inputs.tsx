'use client';

import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import NumberInput from '@/components/ui/molecules/number-input';

const NumberInputs = () => {
  return (
    <div className="flex flex-col gap-8">
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Number Inputs
      </Typography>

      <GlassPanel className="flex flex-col gap-6 p-8 w-fit">
        <ul className="flex flex-wrap gap-6">
          <li>
            <NumberInput name="number-input" onChange={(value) => console.log(value)} className="min-w-24" />
          </li>
          <li>
            <NumberInput name="number-input-with-unit" unit="min" onChange={console.log} className="min-w-36" />
          </li>
        </ul>
        <ul className="flex flex-wrap gap-6">
          <li>
            <NumberInput
              name="number-input-with-min"
              min="0"
              onChange={(value) => console.log(value)}
              className="min-w-24"
            />
          </li>
          <li>
            <NumberInput
              name="number-input-with-max"
              max="10"
              onChange={(value) => console.log(value)}
              className="min-w-24"
            />
          </li>
          <li>
            <NumberInput
              name="number-input-with-min-and-max"
              min="0"
              max="10"
              onChange={(value) => console.log(value)}
              className="min-w-24"
            />
          </li>
        </ul>
      </GlassPanel>
    </div>
  );
};

export default NumberInputs;
