import Typography from '@/components/ui/atoms/typography';
import Tag from '@/components/ui/molecules/tag';
import GlassPanel from '@/components/ui/atoms/glass-panel';

const Tags = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Tags
      </Typography>
      <div className="flex flex-col gap-8 max-w-md">
        <GlassPanel className="p-8">
          <ul className="flex flex-wrap gap-6">
            <li>
              <Tag variant="primary" label="Primary" />
            </li>
            <li>
              <Tag variant="secondary" label="Secondary" />
            </li>
            <li>
              <Tag variant="accent" label="Accent" />
            </li>
            <li>
              <Tag variant="neutral" label="Neutral" />
            </li>
            <li>
              <Tag variant="error" label="Error" />
            </li>
            <li>
              <Tag variant="success" label="Success" />
            </li>
          </ul>
        </GlassPanel>
        <GlassPanel className="p-8">
          <ul className="flex flex-wrap items-center gap-6">
            <li>
              <Tag variant="accent" size="lg" label="Large" />
            </li>
            <li>
              <Tag variant="accent" size="md" label="Medium" />
            </li>
          </ul>
        </GlassPanel>
        <GlassPanel className="p-8">
          <ul className="flex flex-wrap items-center gap-6">
            <li>
              <Tag variant="accent" label="Uppercase" />
            </li>
            <li>
              <Tag variant="accent" lowerCase label="Lowercase" />
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default Tags;
