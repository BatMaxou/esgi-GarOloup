import { GameRoleEnum } from './enums';

export type RoleSlugLocale = 'fr' | 'en';

/**
 * Slug d'URL localisé pour chaque valeur de `GameRoleEnum`
 * Ajouter une entrée ici suffit pour qu'un nouveau rôle soit reconnu dans les deux langues
 */
export const ROLE_SLUG_BY_LOCALE: Record<GameRoleEnum, Record<RoleSlugLocale, string>> = {
  [GameRoleEnum.VILLAGER]: { fr: 'villageois', en: 'villager' },
  [GameRoleEnum.WEREWOLF]: { fr: 'loup-garou', en: 'werewolf' },
  [GameRoleEnum.SEER]: { fr: 'voyante', en: 'seer' },
  [GameRoleEnum.WITCH]: { fr: 'sorciere', en: 'witch' },
  [GameRoleEnum.WILD_CHILD]: { fr: 'enfant-sauvage', en: 'wild-child' },
  [GameRoleEnum.HUNTER]: { fr: 'chasseur', en: 'hunter' },
  [GameRoleEnum.INFECT_FATHER]: { fr: 'infect-pere', en: 'infect-father' },
  [GameRoleEnum.CUPIDON]: { fr: 'cupidon', en: 'cupidon' },
  [GameRoleEnum.ASSASSIN]: { fr: 'assassin', en: 'assassin' },
  [GameRoleEnum.LOVER]: { fr: 'amoureux', en: 'lover' },
};

/**
 * Associe n'importe quel slug (toutes langues confondues) à son `GameRoleEnum`
 */
const SLUG_TO_ROLE_TYPE: Readonly<Record<string, GameRoleEnum>> = Object.entries(ROLE_SLUG_BY_LOCALE).reduce<
  Record<string, GameRoleEnum>
>((acc, [roleType, slugsByLocale]) => {
  Object.values(slugsByLocale).forEach((slug) => {
    acc[slug] = roleType as GameRoleEnum;
  });
  return acc;
}, {});

/**
 * Résout un slug d'URL (FR ou EN) vers son `GameRoleEnum`
 */
export function slugToRoleType(slug: string): GameRoleEnum | null {
  return SLUG_TO_ROLE_TYPE[slug.toLowerCase()] ?? null;
}

/**
 * Génère le slug d'URL localisé pour un rôle
 */
export function roleTypeToSlug(roleType: GameRoleEnum, locale: RoleSlugLocale): string {
  return ROLE_SLUG_BY_LOCALE[roleType]?.[locale] ?? roleType;
}
