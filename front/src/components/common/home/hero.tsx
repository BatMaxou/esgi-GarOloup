'use client';

import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';

const Hero = () => {
  return (
    <section className="relative min-h-screen flex items-center justify-center isolate p-8">
      <div className="z-2 flex flex-col items-center gap-10 text-shadow-[0_0_8px] text-shadow-(color:--color-dark)">
        <Typography tag="h1" variant="heading-1" textColor="light" special className="text-glow-accent">
          GarOloup
        </Typography>
        <Typography tag="p" variant="subtitle" textColor="light" center>
          Stratégie, bluff et trahison… Survivrez-vous à la nuit ?
        </Typography>
        <ul className="w-full flex flex-col items-center justify-center gap-4 sm:gap-8 sm:flex-row">
          <li className="w-full sm:w-fit">
            <Button variant="accent" size="lg" label="Rejoindre une partie" className="w-full sm:w-fit" />
          </li>
          <li className="w-full sm:w-fit">
            <Button variant="secondary" glass size="lg" label="Découvrir" className="w-full sm:w-fit" />
          </li>
        </ul>

        <ul className="hidden sm:flex items-center gap-10 border-t-2 border-light/40 pt-8">
          <li className="flex flex-col items-center gap-2">
            <Typography variant="heading-3" textColor="light" bold>
              6-20
            </Typography>
            <Typography textColor="light" bold>
              joueurs
            </Typography>
          </li>
          <li className="border-l-2 border-light/40 h-8" />
          <li className="flex flex-col items-center gap-2">
            <Typography variant="heading-3" textColor="light" bold>
              En ligne
            </Typography>
          </li>
          <li className="border-l-2 border-light/40 h-8" />
          <li className="flex flex-col items-center gap-2">
            <Typography variant="heading-3" textColor="light" bold>
              100%
            </Typography>
            <Typography textColor="light" bold>
              gratuit
            </Typography>
          </li>
        </ul>
      </div>
    </section>
  );
};

export default Hero;
