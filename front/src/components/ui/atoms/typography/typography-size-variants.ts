export const typographySizeVariants = {
  body: {
    text: 'text-[1rem] leading-[1.5rem]',
    skeletonLine: 'min-h-[1.5rem]',
  },
  'body-sm': {
    text: 'text-[0.875rem] leading-[1.375rem]',
    skeletonLine: 'min-h-[1.375rem]',
  },
  'body-xs': {
    text: 'text-[0.75rem] leading-[1.25rem]',
    skeletonLine: 'min-h-[1.25rem]',
  },
  'heading-1': {
    text: 'text-[2.5rem] leading-[3rem] xs:text-[4rem] xs:leading-[4.5rem] sm:text-[6.5rem] sm:leading-[7rem]',
    skeletonLine: 'min-h-[3rem] xs:min-h-[4.5rem] sm:min-h-[7rem]',
  },
  'heading-2': {
    text: 'text-[2rem] leading-[2.5rem] sm:text-[4rem] sm:leading-[4.5rem]',
    skeletonLine: 'min-h-[2.5rem] sm:min-h-[4.5rem]',
  },
  'heading-3': {
    text: 'text-[1.5rem] leading-[2rem] sm:text-[2rem] sm:leading-[2.5rem]',
    skeletonLine: 'min-h-[2rem] sm:min-h-[2.5rem]',
  },
  subtitle: {
    text: 'text-[1.375rem] leading-[1.75rem]',
    skeletonLine: 'min-h-[1.75rem]',
  },
  button: {
    text: 'text-[1rem] leading-[1.5rem]',
    skeletonLine: 'min-h-[1.5rem]',
  },
  tag: {
    text: 'text-[0.75rem] leading-[1.25rem]',
    skeletonLine: 'min-h-[1.25rem]',
  },
  controlled: {
    text: '',
    skeletonLine: 'min-h-[1.5rem]',
  },
} as const;

export type TypographySizeVariant = keyof typeof typographySizeVariants;

const v = typographySizeVariants;

export const typographyVariantTextClasses = {
  body: v.body.text,
  'body-sm': v['body-sm'].text,
  'body-xs': v['body-xs'].text,
  'heading-1': v['heading-1'].text,
  'heading-2': v['heading-2'].text,
  'heading-3': v['heading-3'].text,
  subtitle: v.subtitle.text,
  button: v.button.text,
  tag: v.tag.text,
  controlled: v.controlled.text,
} as const satisfies Record<TypographySizeVariant, string>;

export const typographyVariantSkeletonLineClasses = {
  body: v.body.skeletonLine,
  'body-sm': v['body-sm'].skeletonLine,
  'body-xs': v['body-xs'].skeletonLine,
  'heading-1': v['heading-1'].skeletonLine,
  'heading-2': v['heading-2'].skeletonLine,
  'heading-3': v['heading-3'].skeletonLine,
  subtitle: v.subtitle.skeletonLine,
  button: v.button.skeletonLine,
  tag: v.tag.skeletonLine,
  controlled: v.controlled.skeletonLine,
} as const satisfies Record<TypographySizeVariant, string>;
