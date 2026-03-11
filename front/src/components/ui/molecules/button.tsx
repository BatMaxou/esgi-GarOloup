import { cva, type VariantProps } from 'class-variance-authority';
import Typography from '@/components/ui/atoms/typography';
import { formatCva } from '@/utils/format';

const buttonCva = cva(
  'h-fit flex items-center justify-center cursor-pointer rounded-xs focus:outline-none transition-colors transition-shadow',
  {
    variants: {
      variant: {
        primary: '',
        secondary: '',
        accent: '',
        neutral: '',
        error: '',
        success: '',
        gradient: formatCva(
          'bg-border-primary-gradient bg-border-hover-primary-gradient bg-border-focus-primary-gradient border-2',
          'hover:text-light',
          'focus:text-light'
        ),
        text: formatCva('bg-transparent', 'hover:underline', 'focus:underline'),
      },
      size: {
        lg: 'px-6 py-3',
        md: 'px-4 py-2',
        sm: 'px-3 py-1',
      },
      full: { true: 'w-full', false: 'w-fit' },
      disabled: { true: 'cursor-not-allowed opacity-50 hover:bg-transparent' },
      glass: {
        true: 'border-2 backdrop-blur-sm hover:shadow-(--button-shadow) hover:inset-shadow-(--button-inset-shadow)',
      },
    },
    compoundVariants: [
      {
        variant: ['primary', 'secondary', 'accent', 'neutral', 'error', 'success', 'gradient'],
        class: formatCva('hover:-translate-y-0.5', 'active:-translate-y-0'),
      },
      {
        glass: true,
        class: formatCva('hover-shadow-(--button-shadow) hover:inset-shadow-(--button-inset-shadow)'),
      },
      {
        variant: 'primary',
        glass: false,
        class: formatCva('bg-primary text-light', 'hover:bg-primary/80', 'focus:bg-primary/80'),
      },
      {
        variant: 'primary',
        glass: true,
        class: formatCva(
          'bg-primary-pastel/20 dark:bg-primary-pastel/10 border-primary/80 text-primary',
          'hover:bg-primary-pastel/40 dark:hover:bg-primary-pastel/20 hover:border-primary',
          'focus:bg-primary-pastel/40 dark:focus:bg-primary-pastel/20 focus:border-primary'
        ),
      },
      {
        variant: 'secondary',
        glass: false,
        class: formatCva('bg-secondary text-light', 'hover:bg-secondary/80', 'focus:bg-secondary/80'),
      },
      {
        variant: 'secondary',
        glass: true,
        class: formatCva(
          'bg-secondary-pastel/20 dark:bg-secondary-pastel/10 border-secondary/80 text-secondary',
          'hover:bg-secondary-pastel/40 dark:hover:bg-secondary-pastel/20 hover:border-secondary',
          'focus:bg-secondary-pastel/40 dark:focus:bg-secondary-pastel/20 focus:border-secondary'
        ),
      },
      {
        variant: 'accent',
        glass: false,
        class: formatCva('bg-accent text-light', 'hover:bg-accent/80', 'focus:bg-accent/80'),
      },
      {
        variant: 'accent',
        glass: true,
        class: formatCva(
          'bg-accent-pastel/20 dark:bg-accent-pastel/10 border-accent/80 text-accent',
          'hover:bg-accent-pastel/40 dark:hover:bg-accent-pastel/20 hover:border-accent',
          'focus:bg-accent-pastel/40 dark:focus:bg-accent-pastel/20 focus:border-accent'
        ),
      },
      {
        variant: 'neutral',
        glass: false,
        class: formatCva('bg-foreground text-background', 'hover:bg-foreground/80', 'focus:bg-foreground/80'),
      },
      {
        variant: 'neutral',
        glass: true,
        class: formatCva(
          'bg-foreground/20 border-foreground/80 text-foreground',
          'hover:bg-foreground/40 hover:border-foreground',
          'focus:bg-foreground/40 focus:border-foreground'
        ),
      },
      {
        variant: 'error',
        glass: false,
        class: formatCva('bg-error text-light', 'hover:bg-error/80', 'focus:bg-error/80'),
      },
      {
        variant: 'error',
        glass: true,
        class: formatCva(
          'bg-error-pastel/60 dark:bg-error-pastel/10 border-error/80 text-error',
          'hover:bg-error-pastel/80 dark:hover:bg-error-pastel/20 hover:border-error',
          'focus:bg-error-pastel/80 dark:focus:bg-error-pastel/20 focus:border-error'
        ),
      },
      {
        variant: 'success',
        glass: false,
        class: formatCva('bg-success text-light', 'hover:bg-success/80', 'focus:bg-success/80'),
      },
      {
        variant: 'success',
        glass: true,
        class: formatCva(
          'bg-success-pastel/60 dark:bg-success-pastel/10 border-success/80 text-success',
          'hover:bg-success-pastel/80 dark:hover:bg-success/20 hover:border-success',
          'focus:bg-success-pastel/80 dark:focus:bg-success/20 focus:border-success'
        ),
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
  href?: string;
  onClick?: () => void;
};

const Button = ({ label, className, asLink, variant, size, full, disabled, glass, ...props }: Props) => {
  const Tag: Tags = asLink ? 'a' : 'button';

  return (
    <Tag className={buttonCva({ variant, size, full, disabled, glass, className })} {...props}>
      <Typography variant="button" textColor="controlled" bold center>
        {label}
      </Typography>
    </Tag>
  );
};

export default Button;
