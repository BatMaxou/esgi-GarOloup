import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { eventCardClasses, eventIconColor } from './config';
import type { JournalEvent } from './types';

type Props = {
  event: JournalEvent;
};

const EventCard = ({ event }: Props) => {
  return (
    <div
      className={`relative flex items-center gap-3 overflow-hidden rounded-sm border bg-[rgba(26,28,46,0.6)] px-4 py-3 backdrop-blur-[12px] before:pointer-events-none before:absolute before:inset-0 before:rounded-sm ${eventCardClasses[event.color]}`}
    >
      <Icon name={event.icon} className={`relative w-5 h-5 shrink-0 ${eventIconColor[event.color]}`} />
      <Typography variant="body-sm" textColor="text" className="relative">
        {event.text}
      </Typography>
    </div>
  );
};

export default EventCard;
