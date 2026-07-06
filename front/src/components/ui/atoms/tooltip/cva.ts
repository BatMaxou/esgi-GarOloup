import { cva } from 'class-variance-authority';

export const tooltipCva = cva(
  [
    'absolute z-50 w-max max-w-xs rounded-sm',
    'border border-primary/15 bg-[rgba(26,28,46,0.85)] backdrop-blur-[16px]',
    'px-3 py-2 text-sm text-primary shadow-lg shadow-black/30',
    'transition-opacity duration-200 ease-out',
  ].join(' '),
  {
    variants: {
      placement: {
        top: 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        bottom: 'top-full left-1/2 -translate-x-1/2 mt-2',
        left: 'right-full top-1/2 -translate-y-1/2 mr-2',
        right: 'left-full top-1/2 -translate-y-1/2 ml-2',
      },
      visibility: {
        hover: 'pointer-events-none opacity-0 group-hover/tooltip:opacity-100',
        visible: 'opacity-100',
        hidden: 'pointer-events-none opacity-0',
      },
    },
    defaultVariants: {
      placement: 'top',
      visibility: 'hover',
    },
  }
);

export const tooltipArrowCva = cva('absolute h-2 w-2 rotate-45 bg-[rgba(26,28,46,0.85)] border-primary/15', {
  variants: {
    placement: {
      top: 'left-1/2 top-full -translate-x-1/2 -translate-y-1/2 border-b border-r',
      bottom: 'left-1/2 bottom-full -translate-x-1/2 translate-y-1/2 border-l border-t',
      left: 'top-1/2 left-full -translate-y-1/2 -translate-x-1/2 border-t border-r',
      right: 'top-1/2 right-full -translate-y-1/2 translate-x-1/2 border-b border-l',
    },
  },
  defaultVariants: {
    placement: 'top',
  },
});
