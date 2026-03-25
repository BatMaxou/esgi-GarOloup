'use client';

import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';

const STEPS = [
  {
    num: 1,
    icon: '🌙',
    title: 'La nuit tombe',
    body: 'Tout le monde ferme les yeux. Les loups désignent une victime en silence. Les rôles spéciaux agissent à leur tour.',
  },
  {
    num: 2,
    icon: '☀️',
    title: 'Le lever du jour',
    body: 'La victime de la nuit est révélée. Le Game Master narre les événements et donne le ton de la partie.',
  },
  {
    num: 3,
    icon: '🗣️',
    title: 'Le débat',
    body: 'Les villageois débattent, accusent, défendent. Chaque joueur a un temps de parole limité pour convaincre.',
  },
  {
    num: 4,
    icon: '🗳️',
    title: 'Le vote',
    body: 'Le village vote pour éliminer un suspect. Le joueur avec le plus de votes est éliminé et son rôle révélé.',
  },
] as const;

const stepNumClassName =
  'flex h-10 min-w-10 shrink-0 items-center justify-center rounded-full bg-linear-(--primary-gradient) font-special text-[1.1rem] text-white shadow-[0_0_16px_rgba(159,154,214,0.25)]';

const Rules = () => {
  return (
    <section id="regles" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[1100px]">
        <Typography tag="div" className="mb-2.5 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase">
          Comment jouer
        </Typography>
        <Typography tag="h2" special className="mb-4 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          Une nuit, une vie.
        </Typography>
        <Typography tag="p" className="font-semibold mb-12 max-w-[560px] text-base leading-[1.7] text-neutral-400">
          Une partie se déroule en cycles Nuit / Jour jusqu&apos;à ce qu&apos;un camp l&apos;emporte.
        </Typography>
        <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
          {STEPS.map(({ num, icon, title, body }) => (
            <Card key={num} orientation="horizontal" className="items-center gap-[18px]">
              <div className={stepNumClassName} aria-hidden>
                {num}
              </div>
              <div className="min-w-0 flex-1">
                <Typography tag="h3" className="mb-1.5 font-title text-[0.95rem] font-bold text-white">
                  <span className="mr-1.5 inline-block align-middle text-[1.1rem]">{icon}</span>
                  {title}
                </Typography>
                <Typography tag="p" className="text-[0.82rem] leading-[1.6] text-neutral-500">
                  {body}
                </Typography>
              </div>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Rules;
