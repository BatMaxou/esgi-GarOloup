import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Divider from '@/components/ui/atoms/divider';

const Dividers = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Dividers
      </Typography>
      <div className="flex flex-col gap-8 w-full sm:flex-row">
        <GlassPanel className="p-8 w-full">
          <ul className="flex flex-col gap-6 w-full">
            <li>
              <Divider className="w-full" />
            </li>
            <li>
              <Divider variant="primary" className="w-full" />
            </li>
            <li>
              <Divider variant="secondary" className="w-full" />
            </li>
            <li>
              <Divider variant="accent" className="w-full" />
            </li>
            <li>
              <Divider variant="success" className="w-full" />
            </li>
            <li>
              <Divider variant="error" className="w-full" />
            </li>
          </ul>
        </GlassPanel>
        <GlassPanel className="p-8 w-full min-h-32">
          <ul className="flex gap-6">
            <li className="h-full">
              <Divider orientation="vertical" className="h-full" />
            </li>
            <li className="h-full">
              <Divider orientation="vertical" variant="primary" className="h-full" />
            </li>
            <li className="h-full">
              <Divider orientation="vertical" variant="secondary" className="h-full" />
            </li>
            <li className="h-full">
              <Divider orientation="vertical" variant="accent" className="h-full" />
            </li>
            <li className="h-full">
              <Divider orientation="vertical" variant="success" className="h-full" />
            </li>
            <li className="h-full">
              <Divider orientation="vertical" variant="error" className="h-full" />
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default Dividers;
