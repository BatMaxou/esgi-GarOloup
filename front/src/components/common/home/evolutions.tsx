'use client';

import { useState } from 'react';

import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import Card from '@/components/ui/molecules/card';
import BugReportModal from '@/components/ui/organisms/modals/bugReportModal';
import IdeaReportModal from '@/components/ui/organisms/modals/ideaReportModal';

const Evolutions = () => {
  const [bugModalOpen, setBugModalOpen] = useState(false);
  const [ideaModalOpen, setIdeaModalOpen] = useState(false);

  return (
    <section id="evolution" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[1100px]">
        <Typography tag="div" className="mb-2.5 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase">
          Plateforme en développement
        </Typography>
        <Typography tag="h2" special className="mb-4 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          Façonnez GarOloup.
        </Typography>
        <Typography tag="p" className="mb-10 max-w-[560px] text-base leading-[1.7] text-neutral-400">
          Le jeu évolue grâce à vous. Un bug, une idée, un rôle à proposer — tout compte.
        </Typography>
        <div className="mb-2 grid grid-cols-1 gap-4 md:grid-cols-2">
          <Card variant="error" className="gap-2">
            <span className="text-[2rem]" aria-hidden>
              🐛
            </span>
            <Typography tag="h3" className="font-title text-base font-bold text-white">
              Signaler un bug
            </Typography>
            <Typography tag="p" className="text-[0.82rem] leading-[1.65] text-neutral-500">
              Vous avez trouvé quelque chose qui cloche ? Une partie qui plante, un rôle qui se comporte bizarrement ?
              Dites-le nous, chaque rapport aide à améliorer l&apos;expérience pour tous.
            </Typography>
            <Button
              variant="error"
              glass
              type="button"
              label="Signaler un bug →"
              className="bg-error/30! border-error/50!"
              onClick={() => setBugModalOpen(true)}
            />
          </Card>
          <Card variant="success" className="gap-2">
            <span className="text-[2rem]" aria-hidden>
              💡
            </span>
            <Typography tag="h3" className="font-title text-base font-bold text-white">
              Proposer une idée
            </Typography>
            <Typography tag="p" className="text-[0.82rem] leading-[1.65] text-neutral-500">
              Un nouveau rôle en tête ? Une mécanique sympa ? Une amélioration de l&apos;interface ? Partagez vos idées
              — les meilleures suggestions finissent dans le jeu.
            </Typography>
            <Button
              variant="success"
              glass
              type="button"
              label="Soumettre une idée →"
              className="bg-success/30! border-success/50!"
              onClick={() => setIdeaModalOpen(true)}
            />
          </Card>
        </div>
        <Card
          orientation="vertical"
          className="gap-3 mt-1 rounded-xs border border-primary/15 bg-primary/6 p-5 text-center"
        >
          <Typography tag="p" className="mb-3 text-[0.82rem] text-neutral-500">
            Vous voulez suivre l&apos;avancement du projet, voter pour les prochaines features et échanger avec les
            autres joueurs ?
          </Typography>
          <Button variant="secondary" glass label="Rejoindre la communauté →" className="self-center" />
        </Card>
      </div>
      <BugReportModal open={bugModalOpen} setOpen={setBugModalOpen} />
      <IdeaReportModal open={ideaModalOpen} setOpen={setIdeaModalOpen} />
    </section>
  );
};

export default Evolutions;
