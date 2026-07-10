import { useTranslations } from 'next-intl';

import JoinButton from './join-button';
import Divider from '@/components/ui/atoms/divider';
import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import { paths } from '@/utils/paths';

const NotFound = () => {
  const t = useTranslations('components.pages.notFound');

  return (
    <main className="relative min-h-screen flex items-center justify-center isolate p-8">
      <section className="z-2 flex flex-col items-center gap-8 sm:gap-10 max-w-2xl">
        <Typography variant="body-sm" textColor="accent" bold uppercase center>
          {t('eyebrow')}
        </Typography>

        <Typography tag="h1" variant="heading-1" special center className="text-glow-accent">
          404
        </Typography>

        <Typography tag="p" variant="subtitle" bold center>
          {t('title')}
        </Typography>

        <Divider className="w-full" />

        <Typography tag="p" variant="body" center>
          {t('description')}
        </Typography>

        <ul className="w-full flex flex-col items-center justify-center gap-4 sm:gap-8 sm:flex-row">
          <li className="w-full sm:w-fit">
            <Button
              variant="accent"
              size="lg"
              popup
              asLink
              href={paths.home}
              label={t('actions.home')}
              className="w-full sm:w-fit"
            />
          </li>
          <li className="w-full sm:w-fit">
            <JoinButton label={t('actions.join')} />
          </li>
        </ul>
      </section>
    </main>
  );
};

export default NotFound;
