<?php
/**
 * Timezone.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @package p2made/yii2-p2y2-users
 * @license MIT
 */

namespace p2m\users\helpers;

use DateTime;
use DateTimeZone;
use yii\helpers\ArrayHelper;

/**
 * class p2m\users\helpers\Timezone
 */
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
