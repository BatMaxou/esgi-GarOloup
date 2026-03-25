'use client';

import { useState } from 'react';

import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Button from '@/components/ui/molecules/button';
import UiDemoModal from '@/components/ui/organisms/modals/uiDemoModal';

const Modals = () => {
  const [open, setOpen] = useState(false);

  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Modales
      </Typography>
      <GlassPanel className="justify-start p-8">
        <div className="flex flex-row justify-start gap-4">
          <Button variant="accent" type="button" label="Ouvrir la modale de démo" onClick={() => setOpen(true)} />
        </div>
      </GlassPanel>

      <UiDemoModal open={open} setOpen={setOpen}>
        <div className="flex flex-col gap-6">
          <Typography tag="p" variant="body" className="text-neutral-400">
            Tu peux imbriquer n&apos;importe quel contenu ici : formulaires, listes, confirmations, etc.
          </Typography>
          <div className="flex flex-wrap justify-end gap-2">
            <Button variant="secondary" type="button" label="Annuler" onClick={() => setOpen(false)} />
            <Button variant="accent" type="button" label="Confirmer" onClick={() => setOpen(false)} />
          </div>
        </div>
      </UiDemoModal>
    </>
  );
};

export default Modals;
