'use client';

import Dot from '@/components/ui/atoms/dot';
import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import Card from '@/components/ui/molecules/card';

const MOCK_GAMES = [
  { code: 'XK-4829', host: 'LoupMaître', playersLabel: '6 / 12 joueurs', fillPercent: 50 },
  { code: 'ZR-0012', host: 'CupidonPro', playersLabel: '3 / 8 joueurs', fillPercent: 37 },
  { code: 'BT-2241', host: 'Villageois_X', playersLabel: '9 / 10 joueurs', fillPercent: 90 },
] as const;

const gameCardClassName =
  'items-center justify-between gap-4 !rounded-xs !border-primary/13 !bg-[rgba(26,28,46,0.55)] !px-5 !py-4 backdrop-blur-md duration-200 hover:!border-primary/30 hover:!bg-[rgba(62,65,90,0.45)] hover:!shadow-none';

const FastJoin = () => {
  return (
    <section id="parties" className="px-6 py-[100px]">
      <div className="mx-auto max-w-[1100px]">
        <Typography
          tag="div"
          className="mb-2.5 flex items-center gap-2 text-[0.72rem] font-bold tracking-[0.16em] text-primary uppercase"
        >
          <Dot variant="live" />
          En direct
        </Typography>
        <Typography tag="h2" special className="mb-4 text-[clamp(2rem,5vw,3rem)] leading-[1.1] text-white">
          Rejoignez la chasse.
        </Typography>
        <Typography tag="p" className="mb-12 max-w-[560px] text-base leading-[1.7] text-neutral-400">
          Des parties ouvertes vous attendent. Glissez-vous dans le village&hellip; ou dans la meute.
        </Typography>
        <div className="mb-8 flex flex-col gap-2.5">
          {MOCK_GAMES.map((game) => (
            <Card key={game.code} liftOnHover={false} orientation="horizontal" className={gameCardClassName}>
              <div className="flex min-w-0 flex-1 items-center gap-4">
                <Dot variant="live" />
                <Typography tag="div" special className="min-w-[72px] shrink-0 text-lg text-accent! text-glow-accent">
                  {game.code}
                </Typography>
                <div className="min-w-0">
                  <Typography tag="div" className="text-[0.85rem] font-semibold text-neutral-200">
                    {game.host}
                  </Typography>
                  <Typography tag="div" className="mt-0.5 text-[0.75rem] text-neutral-500">
                    {game.playersLabel}
                  </Typography>
                  <div className="mt-1.5 h-1 w-20 overflow-hidden rounded-lg bg-primary/15" role="presentation">
                    <div
                      className="h-full rounded-lg bg-linear-(--primary-gradient)"
                      style={{ width: `${game.fillPercent}%` }}
                    />
                  </div>
                </div>
              </div>
              <Button variant="gradient" size="sm" label="Rejoindre →" />
            </Card>
          ))}
        </div>
        <a
          href="#"
          className="group/cta inline-flex cursor-pointer items-center gap-2 rounded-sm border border-primary/30 bg-transparent px-6 py-[11px] text-[0.88rem] font-bold tracking-[0.04em] text-primary-pastel no-underline transition-all duration-200 hover:translate-x-[3px] hover:border-primary/55 hover:bg-primary/10"
        >
          <Typography tag="span" textColor="controlled" className="font-bold">
            Voir toutes les parties{' '}
            <span className="inline-block transition-transform duration-200 group-hover/cta:translate-x-1">→</span>
          </Typography>
        </a>
      </div>
    </section>
  );
};

export default FastJoin;
