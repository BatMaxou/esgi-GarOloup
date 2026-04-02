<?php

namespace App\Service\Filter\Interface;

interface FilterAwareInterface
{
    /** @return string[] */
    public static function provideFilters(): array;
}
