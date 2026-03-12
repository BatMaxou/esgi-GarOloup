import { cva, type VariantProps } from 'class-variance-authority';

import Typography from '@/components/ui/atoms/typography';
import { formatCva } from '@/utils/format';

const buttonCva = cva(
  'h-fit flex items-center justify-center cursor-pointer rounded-sm border-2 transition-all focus:outline-none',
  {
    variants: {
      variant: {
        primary: '',
        secondary: '',
        accent: '',
        neutral: '',
        error: '',
        success: '',
        gradient:
          'bg-linear-(--primary-gradient) bg-origin-border hover:button-shadow-primary focus:button-shadow-primary',
        text: 'bg-transparent hover:underline focus:underline',
      },
      size: {
        lg: 'px-8 py-4',
        md: 'px-6 py-3',
        sm: 'px-4 py-2',
      },
      full: { true: 'w-full', false: 'w-fit' },
      disabled: { true: 'cursor-not-allowed opacity-50 hover:bg-transparent focus:bg-transparent' },
      glass: {
        true: 'border-2 backdrop-blur-sm',
        false: 'border-transparent',
      },
      popup: {
        true: 'transition-transform hover:-translate-y-0.5 active:-translate-y-0',
      },
    },
    compoundVariants: [
      {
        variant: 'primary',
        glass: false,
        class: 'bg-primary text-light hover:button-shadow-primary focus:button-shadow-primary',
      },
      {
        variant: 'primary',
        glass: true,
        class: formatCva(
          'bg-primary-pastel/10 border-primary/80 text-primary',
          'hover:bg-primary-pastel/20 hover:border-primary',
          'focus:bg-primary-pastel/20 focus:border-primary'
        ),
      },
      {
        variant: 'secondary',
        glass: false,
        class: 'bg-secondary text-light hover:button-shadow-secondary focus:button-shadow-secondary',
      },
      {
        variant: 'secondary',
        glass: true,
        class: formatCva(
          'bg-secondary/40 border-secondary-pastel/20 text-light',
          'hover:bg-secondary/60 hover:border-secondary-pastel/40',
          'focus:bg-secondary-pastel/20 focus:border-secondary'
        ),
      },
      {
        variant: 'accent',
        glass: false,
        class: 'bg-accent text-dark hover:button-shadow-accent focus:button-shadow-accent',
      },
      {
        variant: 'accent',
        glass: true,
        class: formatCva(
          'bg-accent-pastel/10 border-accent/80 text-accent',
          'hover:bg-accent/10 hover:border-accent',
          'focus:bg-accent/10 focus:border-accent'
        ),
      },
      {
        variant: 'neutral',
        glass: false,
        class: 'bg-foreground text-dark hover:button-shadow-foreground focus:button-shadow-foreground',
      },
      {
        variant: 'neutral',
        glass: true,
        class: formatCva(
          'bg-foreground/10 border-foreground/20 text-foreground',
          'hover:bg-foreground/20 hover:border-foreground/40',
          'focus:bg-foreground/20 focus:border-foreground/40'
        ),
      },
      {
        variant: 'error',
        glass: false,
        class: 'bg-error text-light hover:button-shadow-error focus:button-shadow-error',
      },
      {
        variant: 'error',
        glass: true,
        class: formatCva(
          'bg-foreground/10 border-foreground/20 text-foreground',
          'hover:bg-error/10 hover:border-error/40 hover:text-error',
          'focus:bg-error/10 focus:border-error/40 focus:text-error'
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
          'bg-foreground/10 border-foreground/20 text-foreground',
          'hover:bg-success/10 hover:border-success/40 hover:text-success',
          'focus:bg-success/10 focus:border-success/40 focus:text-success'
        ),
      },
    ],
    defaultVariants: {
      variant: 'neutral',
      size: 'md',
      full: false,
      disabled: false,
      glass: false,
      popup: false,
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

const Button = ({ label, className, asLink, variant, size, full, disabled, glass, popup, ...props }: Props) => {
  const Tag: Tags = asLink ? 'a' : 'button';

  return (
    <Tag className={buttonCva({ variant, size, full, disabled, glass, popup, className })} {...props}>
      <Typography variant="button" textColor="controlled" bold center>
        {label}
      </Typography>
    </Tag>
  );
};

export default Button;
