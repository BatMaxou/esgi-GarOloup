'use client';

import { useState } from "react";

const LobbyClient = () => {
  const [tab, setTab] = useState<'public' | 'join' | 'create'>('public');
  return (
    <header
      className="relative z-2 flex h-14 w-full shrink-0 items-center justify-center gap-3 border-b border-[rgb(159_154_214/0.13)] bg-[rgb(15_14_28/0.75)] px-4 backdrop-blur-xl"
      aria-label="Barre du salon"
    >
      <div className="flex min-w-0 flex-1 items-center justify-center gap-2.5 sm:flex-none sm:justify-start">
        <div className="flex gap-0 rounded-xs border border-[rgb(159_154_214/0.15)] bg-[rgb(26_28_46/0.6)] p-[3px]">
          <button
            type="button"
            className="cursor-pointer rounded-[calc(var(--radius-xs)-3px)] border-none bg-[linear-gradient(90deg,#b8860b,var(--color-accent))] px-3.5 py-[5px] font-title text-[0.75rem] font-bold tracking-[0.05em] text-neutral-900 shadow-[0_0_12px_rgb(236_167_44/0.3)] transition-all duration-200"
            onClick={() => setTab('public')}
          >
            Publique
          </button>
          <button
            type="button"
            className="cursor-pointer rounded-[calc(var(--radius-xs)-3px)] border-none bg-transparent px-3.5 py-[5px] font-title text-[0.75rem] font-bold tracking-[0.05em] text-neutral-500 transition-all duration-200 hover:text-neutral-300"
            onClick={() => setTab('join')}
          >
            Rejoindre
          </button>
          <button
            type="button"
            className="cursor-pointer rounded-[calc(var(--radius-xs)-3px)] border-none bg-transparent px-3.5 py-[5px] font-title text-[0.75rem] font-bold tracking-[0.05em] text-neutral-500 transition-all duration-200 hover:text-neutral-300"
            onClick={() => setTab('create')}
          >
            Créer
          </button>
        </div>
      </div>
    </header>
  );
};

export default LobbyClient;
