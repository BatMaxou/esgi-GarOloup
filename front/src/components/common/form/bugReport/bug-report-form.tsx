'use client';

import { useFormik } from 'formik';
import cn from 'classnames';

import Button from '@/components/ui/molecules/button';
import TextareaInput from '@/components/ui/molecules/textarea-input';
import SelectInput from '@/components/ui/molecules/select-input';
import { BugReportAreaEnum } from '@/utils/enums';

const AREA_LABELS: Record<BugReportAreaEnum, string> = {
  [BugReportAreaEnum.HOME]: 'Accueil',
  [BugReportAreaEnum.AUTH]: 'Connexion / inscription',
  [BugReportAreaEnum.ACCOUNT]: 'Compte',
  [BugReportAreaEnum.LOBBY]: 'Salon / avant partie',
  [BugReportAreaEnum.GAME]: 'Pendant une partie',
  [BugReportAreaEnum.ROLES]: 'Rôles / règles',
  [BugReportAreaEnum.OTHER]: 'Autre',
};

type Props = {
  className?: string;
  onSubmitted?: () => void;
};

type BugReportFormValues = {
  bugType: string;
  description: string;
};

const BugReportForm = ({ className, onSubmitted }: Props) => {
  const { handleSubmit, handleChange, values } = useFormik({
    initialValues: {
      bugType: '',
      description: '',
    } satisfies BugReportFormValues,
    onSubmit: () => {
      onSubmitted?.();
    },
  });

  const areaOptions = [
    { value: '', label: '— Choisir une zone —' },
    ...Object.values(BugReportAreaEnum).map((area) => ({
      value: area,
      label: AREA_LABELS[area],
    })),
  ];

  return (
    <form onSubmit={handleSubmit} className={cn('flex flex-col gap-4', className)}>
      <SelectInput
        label="Type de problème"
        name="bugType"
        value={values.bugType}
        options={areaOptions}
        onChange={handleChange}
        required
      />
      <TextareaInput
        label="Description"
        name="description"
        value={values.description}
        onChange={handleChange}
        rows={5}
        required
        placeholder="Décris ce qui s’est passé, les étapes pour reproduire le bug, etc."
      />
      <Button variant="accent" label="Envoyer le signalement" type="submit" full />
    </form>
  );
};

export default BugReportForm;
