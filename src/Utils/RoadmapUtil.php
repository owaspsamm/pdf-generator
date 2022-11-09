<?php

declare(strict_types=1);

namespace App\Utils;

use DateTime;

class RoadmapUtil
{
    /**
     * @param array $roadMapDates
     * @return array
     * @throws \Exception
     */
    public function getDefaultDatesForComparison(array $roadMapDates): array
    {
        if (empty($roadMapDates) || (sizeof($roadMapDates) === 1 && $roadMapDates[0] === date("Y-m-d"))) {
            return [new DateTime('now'), new DateTime('-7 days')];
        }

        if (sizeof($roadMapDates) === 1) {
            return [new DateTime($roadMapDates[0]), new DateTime($roadMapDates[0])];
        }
        return [new DateTime($roadMapDates[sizeof($roadMapDates) - 1]), new DateTime($roadMapDates[sizeof($roadMapDates) - 2])];
    }

    /**
     * @return DateTime[]
     */
    public function getDefaultDatesForLineChart(): array
    {
        return [new DateTime('-2 years'), new DateTime('now')];
    }
}