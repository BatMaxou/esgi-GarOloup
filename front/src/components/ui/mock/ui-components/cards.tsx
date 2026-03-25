import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import GlassPanel from '@/components/ui/atoms/glass-panel';

const Cards = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Cards
      </Typography>
      <div className="flex flex-col gap-8">
        <GlassPanel className="p-8 justify-start">
          <ul className="flex flex-row flex-wrap gap-6">
            <li>
              <Card type="role" variant="loup">
                <Typography
                  tag="span"
                  className="absolute top-3 right-3 rounded-xxs border border-accent/30 bg-accent/15 px-[7px] py-0.5 text-[0.8rem]! font-bold tracking-[0.08em] text-accent! uppercase"
                >
                  Nouveau
                </Typography>
                <Typography tag="span" className="mt-8 mb-3.5 block text-[2.2rem]">
                  🐺
                </Typography>
                <Typography tag="div" special className="mb-1.5 text-xl text-white">
                  Loup-Garou
                </Typography>
                <Typography
                  tag="div"
                  className="mb-2.5 text-[0.68rem] font-bold tracking-[0.1em] text-error! uppercase"
                >
                  Camp des Loups
                </Typography>
                <Typography tag="p" className="text-[0.8rem] leading-[1.6] text-neutral-500">
                  Chaque nuit, les loups choisissent une victime. Le jour, ils se fondent parmi les villageois.
                </Typography>
              </Card>
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default Cards;
