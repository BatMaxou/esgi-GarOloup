'use client';

import { useFormik } from 'formik';
import cn from 'classnames';

import Button from '@/components/ui/molecules/button';
import TextareaInput from '@/components/ui/molecules/textarea-input';
import SelectInput from '@/components/ui/molecules/select-input';
import { IdeaCategoryEnum } from '@/utils/enums';

export const IDEA_CATEGORY_LABELS: Record<IdeaCategoryEnum, string> = {
  [IdeaCategoryEnum.NEW_ROLE]: 'Nouveau rôle ou variante',
  [IdeaCategoryEnum.GAME_MECHANIC]: 'Mécanique / règles de partie',
  [IdeaCategoryEnum.UI_UX]: 'Interface & expérience utilisateur',
  [IdeaCategoryEnum.GAME_MASTER]: 'Mode Game Master / narration',
  [IdeaCategoryEnum.LOBBY_PARTY]: 'Salon, création ou rejoindre une partie',
  [IdeaCategoryEnum.SOCIAL_CHAT]: 'Social, chat, invitations',
  [IdeaCategoryEnum.ACCESSIBILITY]: 'Accessibilité',
  [IdeaCategoryEnum.OTHER]: 'Autre',
};

type Props = {
  className?: string;
  onSubmitted?: () => void;
};

type IdeaReportFormValues = {
  category: string;
  description: string;
};

const IdeaReportForm = ({ className, onSubmitted }: Props) => {
  const { handleSubmit, handleChange, values } = useFormik({
    initialValues: {
      category: '',
      description: '',
    } satisfies IdeaReportFormValues,
    onSubmit: () => {
      onSubmitted?.();
    },
  });

  const categoryOptions = [
    { value: '', label: '— Choisir une catégorie —' },
    ...Object.values(IdeaCategoryEnum).map((category) => ({
      value: category,
      label: IDEA_CATEGORY_LABELS[category],
    })),
  ];

  return (
    <form onSubmit={handleSubmit} className={cn('flex flex-col gap-4', className)}>
      <SelectInput
        label="Catégorie"
        name="category"
        value={values.category}
        options={categoryOptions}
        onChange={handleChange}
        required
      />
      <TextareaInput
        label="Ton idée"
        name="description"
        value={values.description}
        onChange={handleChange}
        rows={5}
        required
        placeholder="Explique ton idée : contexte, ce que ça apporterait aux joueurs, exemples éventuels…"
      />
      <Button variant="success" label="Envoyer l'idée" type="submit" full />
    </form>
  );
};

export default IdeaReportForm;
