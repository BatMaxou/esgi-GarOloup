import cn from 'classnames';

import Typography from '@/components/ui/atoms/typography';

const Gradient = ({ gradient, name }: { gradient: string; name: string }) => {
  return (
    <div className="flex flex-col gap-2 items-center">
      <div className={cn('w-45 h-15 rounded bg-no-repeat', gradient)} />
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
        <ul className="bg-neutral-100 rounded-sm p-8 flex flex-wrap gap-6 shadow-(--shadow) w-fit">
          <li>
            <Gradient gradient="bg-linear-(--background-gradient)" name="Background Gradient" />
          </li>
          <li>
            <Gradient gradient="bg-linear-(--primary-gradient)" name="Primary Gradient" />
          </li>
        </ul>
      </div>
    </>
  );
};

export default Gradients;
