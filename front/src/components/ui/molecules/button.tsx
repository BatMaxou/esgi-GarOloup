import { cva, type VariantProps } from 'class-variance-authority';
import Typography from '@/components/ui/atoms/typography';

const buttonCva = cva('flex items-center cursor-pointer rounded-lg focus:outline-none', {
  variants: {
    variant: {
      primary: 'bg-border-primary-gradient bg-border-hover-primary-gradient bg-border-focus-primary-gradient border-2 hover:text-light focus:text-light',
      text: 'bg-transparent',
    },
    size: {
      lg: 'px-3.5 py-2 h-fit xs:h-[48px]',
      md: 'px-3.5 py-2 h-fit xs:h-[40px]',
      sm: 'px-3.5 py-1 h-fit xs:h-[32px]',
    },
    full: {
      true: 'w-full',
      false: 'w-fit',
    },
  },
  compoundVariants: [
  ],
  defaultVariants: {
    variant: 'primary',
    size: 'md',
    full: false,
  },
});

type Tags = 'a' | 'button';

type Props = VariantProps<typeof buttonCva> & {
  label?: string;
  className?: string;
  asLink?: boolean;
};

const Button = ({ label, className, asLink, variant, size, full, ...props }: Props) => {
  const Tag: Tags = asLink ? 'a' : 'button';

  return (
    <Tag className={buttonCva({ variant, size, full,  className })} {...props}>
      <Typography variant="button" textColor="controlled" bold center>{label}</Typography>
    </Tag>
  );
};

export default Button;
