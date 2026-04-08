'use client';

import { useTranslations } from 'next-intl';
import { Fragment } from 'react';

import Divider from '@/components/ui/atoms/divider';
import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import { useAuth } from '@/contexts/auth-context';
import { useRouter } from '@/i18n/navigation';
import { paths } from '@/utils/paths';

const HIGHLIGHTS_NUMBER = 3;

const Hero = () => {
  const tg = useTranslations();
  const t = useTranslations('components.common.home.hero');
  const { user } = useAuth();
  const router = useRouter();

  const handleJoinGame = () => {
    if (user) {
      router.push(paths.lobby);
      return;
    } else {
      router.push(paths.login);
    }
  };

  return (
    <section className="relative min-h-screen flex items-center justify-center isolate p-8">
      <div className="z-2 flex flex-col items-center gap-10 text-shadow-[0_0_8px] text-shadow-(color:--color-dark)">
        <Typography tag="h1" variant="heading-1" textColor="light" special className="text-glow-accent">
          {tg('name')}
        </Typography>
        <Typography tag="p" variant="subtitle" textColor="light" center>
          {t('description')}
        </Typography>
        <ul className="w-full flex flex-col items-center justify-center gap-4 sm:gap-8 sm:flex-row">
          <li className="w-full sm:w-fit">
            <Button variant="accent" onClick={handleJoinGame} size="lg" popup label={t('actions.join')} className="w-full sm:w-fit" />
          </li>
          <li className="w-full sm:w-fit">
            <Button
              variant="secondary"
              glass
              size="lg"
              popup
              label={t('actions.discover')}
              className="w-full sm:w-fit"
            />
          </li>
        </ul>

        <Divider className="w-full" />

        <ul className="hidden sm:flex items-center gap-10">
          {Array.from({ length: HIGHLIGHTS_NUMBER }).map((_, index) => (
            <Fragment key={index}>
              {index !== 0 && (
                <li key={`divider-${index}`}>
                  <Divider orientation="vertical" className="h-12" />
                </li>
              )}
              <li key={index} className="flex flex-col items-center gap-2">
                <Typography variant="heading-3" textColor="light" bold>
                  {t(`highlights.${index}.title`)}
                </Typography>
                {t.has(`highlights.${index}.description`) && (
                  <Typography textColor="light" bold>
                    {t(`highlights.${index}.description`)}
                  </Typography>
                )}
              </li>
            </Fragment>
          ))}
        </ul>
      </div>
    </section>
  );
};

export default Hero;
