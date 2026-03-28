'use client';

import Dialog from '@/components/ui/molecules/dialog';
import BugReportForm from '@/components/common/form/bugReport/bug-report-form';

export type BugReportModalProps = {
  open: boolean;
  setOpen: (open: boolean) => void;
};

const BugReportModal = ({ open, setOpen }: BugReportModalProps) => {
  return (
    <Dialog
      open={open}
      setOpen={setOpen}
      title="Signaler un bug"
      description="Indique la zone concernée et décris le problème le plus précisément possible."
      size="md"
    >
      <BugReportForm onSubmitted={() => setOpen(false)} />
    </Dialog>
  );
};

export default BugReportModal;
