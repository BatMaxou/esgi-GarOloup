'use client';

import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';

const badgeClassName =
  'absolute top-3 right-3 rounded-xxs border border-accent/30 bg-accent/15 px-[7px] py-0.5 text-[0.8rem]! font-bold tracking-[0.08em] text-accent! uppercase';

const Roles = () => {
  return (
    <section id="roles" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[1100px]">
        <Typography tag="div" className="mb-2.5 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase">
          Des rôles exclusifs
        </Typography>
        <Typography tag="h2" special className="mb-4 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          Qui se cache dans la nuit ?
        </Typography>
        <Typography tag="p" className="font-semibold mb-12 max-w-[560px] text-base leading-[1.7] text-neutral-400">
          Chaque rôle change la donne. Bluff, alliance ou trahison &mdash; à vous de jouer.
        </Typography>
        <div className="mb-9 grid grid-cols-2 gap-4 md:grid-cols-4">
          <Card type="role" variant="loup">
            <Typography tag="span" className={badgeClassName}>
              Nouveau
            </Typography>
            <Typography tag="span" className="mt-8 mb-3.5 block text-[2.2rem]">
              🐺
            </Typography>
            <Typography tag="div" special className="mb-1.5 text-xl text-white">
              Loup-Garou
            </Typography>
            <Typography tag="div" className="mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-error! uppercase">
              Camp des Loups
            </Typography>
            <Typography tag="p" className="text-[0.8rem] leading-[1.6] text-neutral-500">
              Chaque nuit, les loups choisissent une victime. Le jour, ils se fondent parmi les villageois.
            </Typography>
          </Card>
          <Card type="role" variant="sorciere">
            <Typography tag="span" className={badgeClassName}>
              Nouveau
            </Typography>
            <Typography tag="span" className="mt-8 mb-3.5 block text-[2.2rem]">
              🧙‍♀️
            </Typography>
            <Typography tag="div" special className="mb-1.5 text-xl text-white">
              Sorcière
            </Typography>
            <Typography tag="div" className="mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-success uppercase">
              Camp des Villageois
            </Typography>
            <Typography tag="p" className="text-[0.8rem] leading-[1.6] text-neutral-500">
              Possède deux potions : une pour sauver une victime des loups, une pour éliminer n&apos;importe quel
              joueur.
            </Typography>
          </Card>
          <Card type="role" variant="villageois">
            <Typography tag="span" className={badgeClassName}>
              Nouveau
            </Typography>
            <Typography tag="span" className="mt-8 mb-3.5 block text-[2.2rem]">
              🏹
            </Typography>
            <Typography tag="div" special className="mb-1.5 text-xl text-white">
              Chasseur
            </Typography>
            <Typography tag="div" className="mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-success uppercase">
              Camp des Villageois
            </Typography>
            <Typography tag="p" className="text-[0.8rem] leading-[1.6] text-neutral-500">
              À sa mort, il peut emporter une dernière victime avec lui. Une menace même dans la défaite.
            </Typography>
          </Card>
          <Card type="role" variant="gm">
            <Typography tag="span" className={badgeClassName}>
              Nouveau
            </Typography>
            <Typography tag="span" className="mt-8 mb-3.5 block text-[2.2rem]">
              🎭
            </Typography>
            <Typography tag="div" special className="mb-1.5 text-xl text-white">
              Game Master
            </Typography>
            <Typography tag="div" className="mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-accent! uppercase">
              Hors-Jeu
            </Typography>
            <Typography tag="p" className="text-[0.8rem] leading-[1.6] text-neutral-500">
              Ne joue pas, mais anime la partie avec des scripts narrés. Idéal quand tout le monde est dans la même
              pièce.
            </Typography>
          </Card>
        </div>
        <a
          href="#"
          className="group/cta inline-flex cursor-pointer items-center gap-2 rounded-sm border border-primary/30 bg-transparent px-6 py-[11px] text-[0.88rem] font-bold tracking-[0.04em] text-primary-pastel no-underline transition-all duration-200 hover:translate-x-[3px] hover:border-primary/55 hover:bg-primary/10"
        >
          <Typography tag="span" textColor="controlled" className="font-bold">
            Voir tous les rôles{' '}
            <span className="inline-block transition-transform duration-200 group-hover/cta:translate-x-1">→</span>
          </Typography>
        </a>
      </div>
    </section>
  );
};

export default Roles;
