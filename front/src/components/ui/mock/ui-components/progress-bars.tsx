import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import ProgressBar from '@/components/ui/atoms/progress-bar';

const ProgressBars = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Progress Bars
      </Typography>
      <div className="flex w-full">
        <GlassPanel className="p-8 w-full max-w-md">
          <ul className="flex flex-col gap-6 w-full">
            <li>
              <ProgressBar value={0} className="w-full" />
            </li>
            <li>
              <ProgressBar value={1} total={3} className="w-full" />
            </li>
            <li>
              <ProgressBar value={2} total={3} className="w-full" />
            </li>
            <li>
              <ProgressBar value={100} className="w-full" />
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default ProgressBars;
