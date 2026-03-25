'use client';

import Dialog from '@/components/ui/molecules/dialog';
import IdeaReportForm from '@/components/common/form/ideaReport/idea-report-form';

export type IdeaReportModalProps = {
  open: boolean;
  setOpen: (open: boolean) => void;
};

const IdeaReportModal = ({ open, setOpen }: IdeaReportModalProps) => {
  return (
    <Dialog
      open={open}
      setOpen={setOpen}
      title="Proposer une idée"
      description="Choisis une catégorie et décris ton idée : les bonnes suggestions peuvent inspirer la roadmap."
      size="md"
    >
      <IdeaReportForm onSubmitted={() => setOpen(false)} />
    </Dialog>
  );
};

export default IdeaReportModal;
