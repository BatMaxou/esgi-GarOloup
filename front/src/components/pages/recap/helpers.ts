import { GameRoleEnum, GameRuntimeStepEnum, GameTeamEnum } from '@/utils/enums';
import type { PeriodRecap, PlayerRecap, ActionRecap } from '@/utils/types';
import { actionEventMeta } from './config';
import type { JournalEvent, Translate } from './types';

export const areBothLoversAlive = (players: PlayerRecap[]): boolean => {
  const lovers = players.filter((p) => p.isInCouple);

  return lovers.length === 2 && lovers.every((p) => !p.isDead);
};

export const recapPlayerTeam = (player: PlayerRecap, bothLoversAlive: boolean): GameTeamEnum | undefined => {
  if (player.isInCouple || (player.role === GameRoleEnum.CUPIDON && bothLoversAlive)) {
    return GameTeamEnum.COUPLE;
  }

  return player.team;
};

export const resolveActionKey = (action: ActionRecap): keyof typeof actionEventMeta => {
  switch (action.actionType) {
    case 'save':
      return 'witchSave';
    case 'infection':
      return 'infection';
    case 'reveal':
      return 'seerReveal';
    case 'murder':
    default:
      if (action.source === 'witch') return 'witchPoison';
      if (action.source === 'assassin') return 'assassinMurder';
      return 'werewolfMurder';
  }
};

export const buildPeriodEvents = (period: PeriodRecap, players: PlayerRecap[], t: Translate): JournalEvent[] => {
  const nameOf = (id?: string) => players.find((p) => p.playerId === id)?.username ?? '?';
  const events: JournalEvent[] = [];

  period.actions.forEach((action, idx) => {
    const key = resolveActionKey(action);
    const meta = actionEventMeta[key];
    events.push({
      id: `action-${idx}`,
      icon: meta.icon,
      color: meta.color,
      text: t(`components.pages.recap.event.${key}`, { username: nameOf(action.targetPlayerId) }),
    });
  });

  if (period.type === GameRuntimeStepEnum.VOTE) {
    events.push(
      period.eliminatedPlayerId
        ? {
            id: 'vote',
            icon: 'skull',
            color: 'red',
            text: t('components.pages.recap.voteEliminated', { username: nameOf(period.eliminatedPlayerId) }),
          }
        : { id: 'vote', icon: 'users', color: 'neutral', text: t('components.pages.recap.voteNoVictim') }
    );
  }

  if (period.type === GameRuntimeStepEnum.NIGHT && events.length === 0) {
    events.push({ id: 'calm', icon: 'moon', color: 'neutral', text: t('components.pages.recap.event.calmNight') });
  }

  return events;
};
