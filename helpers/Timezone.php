<?php

namespace amnah\yii2\user\helpers;

use DateTime;
use DateTimeZone;
use yii\helpers\ArrayHelper;

class Timezone
{
    /**
     * Get all of the time zones with the offsets sorted by their offset
     * @return array
     */
    public static function getAll()
    {
        $timezones = [];
        $identifiers = DateTimeZone::listIdentifiers();
        foreach ($identifiers as $identifier) {
            $date = new DateTime("now", new DateTimeZone($identifier));
            $offsetText = $date->format("P");
            $offsetInHours = $date->getOffset() / 60 / 60;
            $timezones[] = [
                "identifier" => $identifier,
                "name" => "(GMT{$offsetText}) $identifier",
                "offset" => $offsetInHours
            ];
        }

        ArrayHelper::multisort($timezones, "offset", SORT_ASC, SORT_NUMERIC);
        return $timezones;
    }
}
