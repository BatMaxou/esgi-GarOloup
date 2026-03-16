<?php

namespace App\Event\Subscriber\Kernel;

use App\Domain\GameEvent\Interface\GameEventCollectorInterface;
use App\Domain\GameEvent\Interface\GameEventPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class GameEventPersisterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly GameEventCollectorInterface $gameEventCollector,
        private readonly GameEventPersisterInterface $gameEventPersister,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function onKernelTerminate(): void
    {
        foreach ($this->gameEventCollector->getEvents() as $gameEvent) {
            $this->gameEventPersister->persist($gameEvent);
        }

        $this->em->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }
}
