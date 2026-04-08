import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import SelectInput from '@/components/ui/molecules/select-input';

const DEMO_OPTIONS = [
  { value: '', label: '— Choisir une option —' },
  { value: 'opt-a', label: 'Option A' },
  { value: 'opt-b', label: 'Option B' },
  { value: 'opt-c', label: 'Option C' },
];

const SelectInputs = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Select
      </Typography>

      <GlassPanel className="p-8 w-fit">
        <ul className="flex flex-col gap-6">
          <li>
            <SelectInput sizing="sm" name="select-input-sm" options={DEMO_OPTIONS} defaultValue="" />
          </li>
          <li>
            <SelectInput name="select-input-md" options={DEMO_OPTIONS} defaultValue="" />
          </li>
          <li>
            <SelectInput sizing="lg" name="select-input-lg" options={DEMO_OPTIONS} defaultValue="" />
          </li>
        </ul>
      </GlassPanel>
    </>
  );
};

export default SelectInputs;
