'use client';

import { type ReactNode } from 'react';

import Dialog from '@/components/ui/molecules/dialog';

export type UiDemoModalProps = {
  open: boolean;
  setOpen: (open: boolean) => void;
  children: ReactNode;
  title?: ReactNode;
  description?: ReactNode;
  size?: 'sm' | 'md' | 'lg' | 'xl';
};

const UiDemoModal = ({
  open,
  setOpen,
  children,
  title = 'Modale (démo)',
  description = 'Exemple de contenu dans une fenêtre modale : titre, texte et actions.',
  size = 'md',
}: UiDemoModalProps) => {
  return (
    <Dialog open={open} setOpen={setOpen} title={title} description={description} size={size}>
      {children}
    </Dialog>
  );
};

export default UiDemoModal;
