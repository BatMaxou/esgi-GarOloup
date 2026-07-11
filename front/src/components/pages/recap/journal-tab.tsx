import type { Recap } from '@/utils/types';
import PeriodSection from './period-section';

type Props = {
  recap: Recap;
};

const JournalTab = ({ recap }: Props) => {
  return (
    <div className="flex flex-col gap-6 pt-6">
      {recap.periods.map((period, idx) => (
        <PeriodSection key={idx} period={period} players={recap.players} />
      ))}
    </div>
  );
};

export default JournalTab;
