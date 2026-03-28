'use client';

import { useState } from 'react';

import Typography from '@/components/ui/atoms/typography';
import Dropdown from '@/components/ui/molecules/dropdown';

const FAQ_ITEMS = [
  {
    id: 'players-min',
    question: 'Combien de joueurs faut-il minimum ?',
    answer:
      "Il faut au moins 6 joueurs pour lancer une partie. Au-delà de 20 joueurs, la partie peut devenir difficile à gérer. L'idéal se situe entre 8 et 12.",
  },
  {
    id: 'account',
    question: 'Faut-il créer un compte pour jouer ?',
    answer:
      "Non ! Il suffit de choisir un pseudo et de rejoindre ou créer une partie. Un compte permet de sauvegarder vos stats et pseudos favoris, mais c'est entièrement optionnel.",
  },
  {
    id: 'duration',
    question: 'Combien de temps dure une partie ?',
    answer:
      "Entre 15 et 45 minutes selon le nombre de joueurs et la durée des débats configurée par l'hôte. Une partie à 8 joueurs dure généralement 20 minutes.",
  },
  {
    id: 'gm-mode',
    question: 'Comment fonctionne le mode Game Master ?',
    answer:
      "Le Game Master est un joueur hors-jeu qui anime la partie. Il reçoit des scripts narrés à chaque phase (nuit, lever du jour, mort…) pour maintenir l'immersion. Idéal en présentiel !",
  },
  {
    id: 'irl',
    question: 'Peut-on jouer en présentiel avec GarOloup ?',
    answer:
      'Absolument. GarOloup peut remplacer le jeu physique : un écran partagé ou un téléphone par joueur suffit. Le mode Game Master est particulièrement adapté à cette situation.',
  },
] as const;

const Faq = () => {
  const [openId, setOpenId] = useState<string | null>(null);

  return (
    <section id="faq" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[720px]">
        <Typography tag="div" className="mb-2.5 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase">
          Questions fréquentes
        </Typography>
        <Typography tag="h2" special className="mb-10 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          On répond à tout.
        </Typography>
        <div className="mb-9 flex flex-col gap-2">
          {FAQ_ITEMS.map((item) => (
            <Dropdown
              key={item.id}
              open={openId === item.id}
              onOpenChange={(next) => setOpenId(next ? item.id : null)}
              trigger={item.question}
            >
              {item.answer}
            </Dropdown>
          ))}
        </div>
        <a
          href="#"
          className="group/cta inline-flex cursor-pointer items-center gap-2 rounded-sm border border-primary/30 bg-transparent px-6 py-[11px] text-[0.88rem] font-bold tracking-[0.04em] text-primary-pastel no-underline transition-all duration-200 hover:translate-x-[3px] hover:border-primary/55 hover:bg-primary/10"
        >
          <Typography tag="span" textColor="controlled" className="font-bold">
            Voir toutes les questions{' '}
            <span className="inline-block transition-transform duration-200 group-hover/cta:translate-x-1">→</span>
          </Typography>
        </a>
      </div>
    </section>
  );
};

export default Faq;
