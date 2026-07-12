<?php

namespace App\Entity\Game\Period\Interface;

use Doctrine\Common\Collections\ReadableCollection;

interface ActionPeriodInterface extends PeriodInterface
{
    /**
     * @return ReadableCollection<int, PeriodAction>
     */
    public function getActions(): ReadableCollection;
}
