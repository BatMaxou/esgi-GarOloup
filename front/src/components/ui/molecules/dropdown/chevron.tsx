import cn from 'classnames';

type Props = {
  className?: string;
  open?: boolean;
};

const DropdownChevron = ({ className, open }: Props) => (
  <span
    className={cn(
      'shrink-0 text-[0.8rem] text-neutral-600 transition-transform duration-250',
      open && 'rotate-180 text-primary',
      className
    )}
    aria-hidden
  >
    ▼
  </span>
);

export default DropdownChevron;
