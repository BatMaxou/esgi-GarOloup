import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import TextInput from '@/components/ui/atoms/text-input';

const TextInputs = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Text Inputs
      </Typography>
      <div className="flex flex-wrap gap-8">
        <GlassPanel className="p-8 w-fit">
          <ul className="flex flex-col gap-6">
            <li>
              <TextInput sizing="sm" name="text-input-sm" />
            </li>
            <li>
              <TextInput name="text-input" />
            </li>
            <li>
              <TextInput sizing="lg" name="text-input-lg" />
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default TextInputs;
