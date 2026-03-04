import { ReactNode } from 'react';
import { cva, type VariantProps } from 'class-variance-authority';

const typographyCva = cva('antialiased', {
  variants: {
    variant: {
      body: 'text-[0.875rem] leading-[1.375rem]',
      'body-lg': 'text-[1rem] leading-[1.5rem]',
      'body-sm': 'text-[0.75rem] leading-[1.25rem]',
      'body-xs': 'text-[0.625rem] leading-[1rem]',
      'heading-1':
        'text-[2rem] leading-[2.5rem] sm:text-[2.5rem] sm:leading-[3rem]',
      'heading-2':
        'text-[1.625rem] leading-[2.125rem] sm:text-[2rem] sm:leading-[2.5rem]',
      'heading-3': 'text-[1.25rem] leading-[1.875rem] sm:text-[1.375rem]',
      subtitle: 'text-[1.25rem] leading-[1.75rem]',
      input: 'text-[0.75rem] leading-[1.25rem]',
      button: 'text-[0.875rem] leading-[1.375rem]',
    },
    textColor: {
      controlled: 'text-inherit',
      text: 'text-base-text',
      light: 'text-light',
      primary: 'text-primary',
      'primary-2': 'text-primary-2',
    },
    bold: { true: 'font-bold' },
    center: { true: 'text-center' },
    underline: { true: 'underline' },
    ellipsis: { true: 'truncate' },
    special: { true: 'font-special' },
  },
  compoundVariants: [
    {
      special: false,
      variant: ['heading-1', 'heading-2', 'heading-3', 'subtitle'],
      class: 'font-title',
    },
    {
      special: false,
      variant: ['body', 'body-lg', 'body-sm', 'body-xs', 'input', 'button'],
      class: 'font-normal',
    },
  ],
  defaultVariants: {
    variant: 'body',
    textColor: 'text',
    bold: false,
    center: false,
    underline: false,
    ellipsis: false,
    special: false,
  },
});

type Tags = 'span' | 'div' | 'p' | 'h1' | 'h2' | 'h3' | 'a';

type Props = VariantProps<typeof typographyCva> & {
  children: ReactNode;
  className?: string;
  tag?: Tags;
};

const Typography = ({
  children,
  className,
  tag,
  variant,
  textColor,
  bold,
  center,
  underline,
  ellipsis,
  special,
  ...props
}: Props) => {
  const Tag: Tags = tag || 'span';

  return (
    <Tag
      className={typographyCva({
        variant,
        textColor,
        bold,
        center,
        underline,
        ellipsis,
        special,
        className,
      })}
      {...props}
    >
      {children}
    </Tag>
  );
};

export default Typography;
