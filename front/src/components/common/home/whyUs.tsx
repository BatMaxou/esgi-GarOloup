'use client';

import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';

const ITEMS = [
  {
    icon: '🎭',
    title: 'Mode Game Master',
    body: "Un joueur endosse le rôle de narrateur. Des scripts lui sont fournis à chaque phase — lever du jour, rebondissements, mort dramatique. Parfait pour mettre l'ambiance quand vous êtes tous dans la même pièce.",
    featured: true,
  },
  {
    icon: '⚡',
    title: 'Lancez une partie en 30s',
    body: "Un code, un lien partagé, et c'est parti. Aucune installation, aucun compte requis pour rejoindre une partie.",
  },
  {
    icon: '🎲',
    title: 'Rôles & variantes',
    body: 'Des dizaines de rôles disponibles, des configurations personnalisables. Chaque partie est unique.',
  },
  {
    icon: '🌐',
    title: '100% en ligne',
    body: "Jouez avec vos amis à distance, en visio ou juste via le chat intégré. La distance n'est plus une excuse.",
  },
  {
    icon: '🆓',
    title: 'Entièrement gratuit',
    body: "Pas d'abonnement, pas de microtransactions. GarOloup est et restera gratuit pour tous.",
  },
  {
    icon: '🛠️',
    title: 'En constante évolution',
    body: 'Nouveaux rôles, nouvelles mécaniques, corrections régulières. La communauté guide le développement.',
  },
];

const iconClassName = 'mb-3.5 block text-[1.8rem]';

const WhyUs = () => {
  return (
    <section id="pourquoi" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[1100px]">
        <Typography tag="div" className="mb-2.5 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase">
          Pourquoi GarOloup
        </Typography>
        <Typography tag="h2" special className="mb-4 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          Le loup-garou réinventé.
        </Typography>
        <Typography tag="p" className="font-semibold mb-12 max-w-[560px] text-base leading-[1.7] text-neutral-400">
          Pas d&apos;application à télécharger, pas de compte obligatoire. Juste un lien et vos amis.
        </Typography>
        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
          {ITEMS.map(({ icon, title, body, featured }) => (
            <Card key={title} variant={featured === true ? 'accent' : 'none'} className="px-6 py-7">
              <span className={iconClassName} aria-hidden>
                {icon}
              </span>
              <Typography
                tag="h3"
                className={
                  featured === true
                    ? 'mb-2 font-title text-[0.95rem] font-bold text-accent!'
                    : 'mb-2 font-title text-[0.95rem] font-bold text-white'
                }
              >
                {title}
              </Typography>
              <Typography tag="p" className="text-[0.82rem] leading-[1.65] text-neutral-500">
                {body}
              </Typography>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default WhyUs;
