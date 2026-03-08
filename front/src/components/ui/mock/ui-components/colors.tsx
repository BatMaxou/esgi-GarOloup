import cn from 'classnames';

import Typography from '@/components/ui/atoms/typography';

const Color = ({ color, name }: { color: string; name: string }) => {
  return (
    <div className="flex flex-col gap-2 items-center">
      <div className={cn('w-15 h-15 rounded shadow-(--shadow)', color)} />
      <Typography center>{name}</Typography>
    </div>
  );
};

const Colors = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Colors
      </Typography>
      <div className="flex flex-col flex-wrap gap-8">
        <ul className="bg-background rounded-sm p-8 flex flex-wrap gap-6 shadow-(--shadow) w-fit">
          <li>
            <Color color="bg-primary" name="Primary" />
          </li>
          <li>
            <Color color="bg-primary-2" name="Primary 2" />
          </li>
        </ul>
        <ul className="bg-background rounded-sm p-8 flex flex-wrap gap-6 shadow-(--shadow) w-fit">
          <li>
            <Color color="bg-primary/20" name="20%" />
          </li>
          <li>
            <Color color="bg-primary/40" name="40%" />
          </li>
          <li>
            <Color color="bg-primary/60" name="60%" />
          </li>
          <li>
            <Color color="bg-primary/80" name="80%" />
          </li>
          <li>
            <Color color="bg-primary" name="100%" />
          </li>
        </ul>
        <ul className="bg-background rounded-sm p-8 flex flex-wrap gap-6 shadow-(--shadow) w-fit">
          <li>
            <Color color="bg-neutral-100" name="Neutral 100" />
          </li>
          <li>
            <Color color="bg-neutral-200" name="Neutral 200" />
          </li>
          <li>
            <Color color="bg-neutral-300" name="Neutral 300" />
          </li>
          <li>
            <Color color="bg-neutral-400" name="Neutral 400" />
          </li>
          <li>
            <Color color="bg-neutral-500" name="Neutral 500" />
          </li>
          <li>
            <Color color="bg-neutral-600" name="Neutral 600" />
          </li>
          <li>
            <Color color="bg-neutral-700" name="Neutral 700" />
          </li>
          <li>
            <Color color="bg-neutral-800" name="Neutral 800" />
          </li>
          <li>
            <Color color="bg-neutral-900" name="Neutral 900" />
          </li>
        </ul>
        <ul className="bg-background rounded-sm p-8 flex flex-wrap gap-6 shadow-(--shadow) w-fit">
          <li>
            <Color color="bg-secondary" name="Secondary" />
          </li>
          <li>
            <Color color="bg-secondary-pastel" name="Secondary Pastel" />
          </li>
        </ul>
        <ul className="bg-background rounded-sm p-8 flex flex-wrap gap-6 shadow-(--shadow) w-fit">
          <li>
            <Color color="bg-success" name="Success" />
          </li>
          <li>
            <Color color="bg-success-pastel" name="Success Pastel" />
          </li>
          <li>
            <Color color="bg-error" name="Error" />
          </li>
          <li>
            <Color color="bg-error-pastel" name="Error Pastel" />
          </li>
        </ul>
      </div>
    </>
  );
};

export default Colors;
