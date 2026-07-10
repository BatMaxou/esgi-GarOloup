'use client';

import { useState } from 'react';
import cn from 'classnames';
import { useTranslations } from 'next-intl';
import Link from 'next/link';

import Icon from '@/components/ui/atoms/icon';
import Card from '@/components/ui/molecules/card';
import BugReportModal from '@/components/ui/organisms/modals/bugReportModal';
import IdeaReportModal from '@/components/ui/organisms/modals/ideaReportModal';

type ColumnId = 'play' | 'discover' | 'project';

type FooterLink =
  | { type: 'href'; href: string; labelKey: string }
  | { type: 'modal'; modal: 'bug' | 'idea'; labelKey: string };

const FOOTER_NAV: { column: ColumnId; links: FooterLink[] }[] = [
  {
    column: 'play',
    links: [
      { type: 'href', href: '#', labelKey: 'joinGame' },
      { type: 'href', href: '#', labelKey: 'createGame' },
      { type: 'href', href: '#', labelKey: 'ongoing' },
      { type: 'href', href: '#', labelKey: 'leaderboard' },
    ],
  },
  {
    column: 'discover',
    links: [
      { type: 'href', href: '#', labelKey: 'allRoles' },
      { type: 'href', href: '#', labelKey: 'rules' },
      { type: 'href', href: '#', labelKey: 'gmMode' },
      { type: 'href', href: '#', labelKey: 'faq' },
    ],
  },
  {
    column: 'project',
    links: [
      { type: 'href', href: '#', labelKey: 'roadmap' },
      { type: 'modal', modal: 'bug', labelKey: 'reportBug' },
      { type: 'modal', modal: 'idea', labelKey: 'suggestIdea' },
      { type: 'href', href: '#', labelKey: 'community' },
    ],
  },
];

const colLinkClassName =
  'inline-block w-fit text-[0.83rem] text-neutral-500 no-underline transition-all duration-150 hover:translate-x-[3px] hover:text-primary-pastel';

const modalTriggerClassName = cn(
  colLinkClassName,
  'cursor-pointer border-0 bg-transparent p-0 text-left font-[inherit]'
);

const Footer = () => {
  const [bugModalOpen, setBugModalOpen] = useState(false);
  const [ideaModalOpen, setIdeaModalOpen] = useState(false);
  const t = useTranslations('components.common.layout.footer');

  return (
    <>
      <footer className="relative z-10 mt-0 overflow-hidden border-t border-primary/13 bg-[rgba(26,28,46,0.85)] backdrop-blur-xl">
        <div
          className="pointer-events-none absolute bottom-[-60px] left-1/2 h-[200px] w-[600px] max-w-[100vw] -translate-x-1/2 bg-[radial-gradient(ellipse,rgba(159,154,214,0.08)_0%,transparent_70%)]"
          aria-hidden
        />
        <div className="relative mx-auto grid max-w-[1100px] grid-cols-1 gap-10 px-8 pb-10 pt-[60px] lg:grid-cols-[1.4fr_2fr] lg:gap-[60px]">
          <div>
            <div className="mb-3.5 font-special text-[2rem] leading-none text-glow-accent">GarOloup</div>
            <p className="mb-6 text-[0.83rem] leading-[1.7] text-neutral-500">
              {t('taglineLine1')}
              <br />
              {t('taglineLine2')}
            </p>
            <div className="flex gap-2">
              <Link href="#" title={t('social.discord')} aria-label={t('social.discord')}>
                <Card orientation="horizontal" className="flex items-center justify-center p-5! cursor-pointer">
                  <Icon name="discord" className="w-6 h-6" />
                </Card>
              </Link>
              <Link
                href="https://github.com/BatMaxou/esgi-GarOloup"
                target="_blank"
                title={t('social.github')}
                aria-label={t('social.github')}
              >
                <Card orientation="horizontal" className="p-5! cursor-pointer">
                  <Icon name="github" className="w-6 h-6" />
                </Card>
              </Link>
              <Link href="#" title={t('social.twitter')} aria-label={t('social.twitter')}>
                <Card orientation="horizontal" className="p-5! cursor-pointer">
                  <Icon name="x" className="w-6 h-6" />
                </Card>
              </Link>
            </div>
          </div>
          <nav className="grid grid-cols-2 gap-8 md:grid-cols-3" aria-label={t('navAriaLabel')}>
            {FOOTER_NAV.map(({ column, links }) => (
              <div key={column} className="flex flex-col gap-2.5">
                <div className="mb-1 text-[0.72rem] font-bold tracking-[0.12em] text-secondary-pastel uppercase">
                  {t(`columns.${column}.title`)}
                </div>
                {links.map((item) =>
                  item.type === 'modal' ? (
                    <button
                      key={item.labelKey}
                      type="button"
                      className={modalTriggerClassName}
                      onClick={() => (item.modal === 'bug' ? setBugModalOpen(true) : setIdeaModalOpen(true))}
                    >
                      {t(`columns.${column}.${item.labelKey}`)}
                    </button>
                  ) : (
                    <Link key={item.labelKey} href={item.href} className={colLinkClassName}>
                      {t(`columns.${column}.${item.labelKey}`)}
                    </Link>
                  )
                )}
              </div>
            ))}
          </nav>
        </div>
        <div className="relative mx-auto flex max-w-[1100px] flex-col items-start gap-3 border-t border-primary/8 px-8 py-5 sm:flex-row sm:items-center sm:justify-between sm:gap-3">
          <span className="text-xs text-neutral-600">{t('copyright')}</span>
          <div className="flex flex-wrap items-center gap-2.5">
            <Link href="#" className="text-xs text-neutral-600 no-underline transition-colors hover:text-neutral-400">
              {t('legal.legalNotice')}
            </Link>
            <span className="text-[0.7rem] text-neutral-700" aria-hidden>
              ·
            </span>
            <Link href="#" className="text-xs text-neutral-600 no-underline transition-colors hover:text-neutral-400">
              {t('legal.privacy')}
            </Link>
            <span className="text-[0.7rem] text-neutral-700" aria-hidden>
              ·
            </span>
            <Link href="#" className="text-xs text-neutral-600 no-underline transition-colors hover:text-neutral-400">
              {t('legal.terms')}
            </Link>
          </div>
        </div>
      </footer>
      <BugReportModal open={bugModalOpen} setOpen={setBugModalOpen} />
      <IdeaReportModal open={ideaModalOpen} setOpen={setIdeaModalOpen} />
    </>
  );
};

export default Footer;
