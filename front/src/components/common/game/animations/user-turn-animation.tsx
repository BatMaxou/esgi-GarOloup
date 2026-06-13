import Lottie from 'lottie-react';
import { useState } from 'react';

import anim from '@/assets/lottie/user-turn.json';

export function UserTurnAnimation({ animateOnce }: { animateOnce?: boolean }) {
  const [isVisible, setIsVisible] = useState(true);

  if (animateOnce && !isVisible) {
    return null;
  }

  return (
    <div className="fixed inset-0 z-9999 bg-transparent">
      <Lottie animationData={anim} loop={!animateOnce} autoplay onComplete={() => animateOnce && setIsVisible(false)} />
    </div>
  );
}