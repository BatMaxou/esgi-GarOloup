import cn from 'classnames';

import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';

const Gradient = ({ gradient, name }: { gradient: string; name: string }) => {
  return (
    <div className="flex flex-col gap-2 items-center">
      <div className={cn('w-25 sm:w-45 h-15 rounded bg-no-repeat shadow-(--shadow)', gradient)} />
      <Typography center>{name}</Typography>
    </div>
  );
};

const Gradients = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Gradients
      </Typography>
      <div className="flex flex-col flex-wrap gap-8">
        <GlassPanel className="p-8 w-fit">
          <ul className="flex flex-wrap justify-center gap-6">
            <li>
              <Gradient gradient="bg-linear-(--background-gradient)" name="Background Gradient" />
            </li>
            <li>
              <Gradient gradient="bg-linear-(--primary-gradient)" name="Primary Gradient" />
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default Gradients;
