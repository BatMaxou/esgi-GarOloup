import { cva, type VariantProps } from 'class-variance-authority';
import Typography from '@/components/ui/atoms/typography';
import { formatCva } from '@/utils/format';

const buttonCva = cva(
  'flex items-center cursor-pointer rounded-lg focus:outline-none transition-colors',
  {
    variants: {
      variant: {
        neutral: '',
        primary: '',
        secondary: '',
        gradient:
          'bg-border-primary-gradient bg-border-hover-primary-gradient bg-border-focus-primary-gradient border-2 hover:text-light focus:text-light',
        text: 'bg-transparent',
      },
      size: {
        lg: 'px-3.5 py-2 h-fit xs:h-[48px]',
        md: 'px-3.5 py-2 h-fit xs:h-[40px]',
        sm: 'px-3.5 py-1 h-fit xs:h-[32px]',
      },
      full: { true: 'w-full', false: 'w-fit' },
      disabled: { true: 'cursor-not-allowed opacity-50 hover:bg-transparent' },
      glass: { true: 'border backdrop-blur-sm' },
    },
    compoundVariants: [
      {
        variant: 'neutral',
        glass: false,
        class: formatCva(
          'text-neutral-200 bg-neutral-600/80 hover:bg-neutral-600',
          'dark:text-neutral-600 dark:bg-neutral-400/90 dark:hover:bg-neutral-400'
        ),
      },
      {
        variant: 'neutral',
        glass: true,
        class: formatCva(
          'text-neutral-600 bg-neutral-600/20 border-neutral-600/80 hover:bg-neutral-600/30',
          'dark:text-neutral-400 dark:bg-neutral-400/20 dark:border-neutral-400/60 dark:hover:bg-neutral-400/30'
        ),
      },
      {
        variant: 'primary',
        glass: false,
        class:
          'bg-primary/90 text-light hover:bg-primary dark:bg-primary dark:hover:bg-primary/90',
      },
      {
        variant: 'primary',
        glass: true,
        class:
          'bg-primary/60 border-primary/80 text-primary dark:bg-primary/40 dark:border-primary/60',
      },
      {
        variant: 'secondary',
        glass: false,
        class:
          'bg-secondary/90 text-light hover:bg-secondary dark:bg-secondary dark:hover:bg-secondary/90',
      },
      {
        variant: 'secondary',
        glass: true,
        class:
          'bg-secondary/60 border-secondary/80 text-secondary dark:bg-secondary/40 dark:border-secondary/60',
      },
    ],
    defaultVariants: {
      variant: 'neutral',
      size: 'md',
      full: false,
      disabled: false,
      glass: false,
    },
  }
);

type Tags = 'a' | 'button';

type Props = VariantProps<typeof buttonCva> & {
  label?: string;
  className?: string;
  asLink?: boolean;
  onClick?: () => void;
};

const Button = ({
  label,
  className,
  asLink,
  variant,
  size,
  full,
  disabled,
  glass,
  ...props
}: Props) => {
  const Tag: Tags = asLink ? 'a' : 'button';

  return (
    <Tag
      className={buttonCva({ variant, size, full, disabled, glass, className })}
      {...props}
    >
      <Typography variant="button" textColor="controlled" bold center>
        {label}
      </Typography>
    </Tag>
  );
};

export default Button;
