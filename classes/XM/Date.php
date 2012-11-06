<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Date extends Kohana_Date {
	/**
	 * Formats a date for multiple columns, ie, day, month, year.
	 * Will add _day, _month, _year to the $field variable for each field.
	 *
	 * @return  string
	 */
	public static function multi_col_date_format($date, $field) {
		$day_field = $field . '_day';
		$month_field = $field . '_month';
		$year_field = $field . '_year';

		if (is_object($date)) {
			$date = array(
				$day_field => $date->$day_field,
				$month_field => $date->$month_field,
				$year_field => $date->$year_field,
			);
		}

		$has_day = ( ! empty($date[$field . '_day']));
		$has_month = ( ! empty($date[$field . '_month']));
		$has_year = ( ! empty($date[$year_field]));

		if ($has_month) {
			$month = date('F', Date::MONTH * $date[$month_field] - (Date::DAY * 2));
		}

		if ($has_day && $has_month && $has_year) {
			return $month . ' ' . $date[$day_field] . ', ' . $date[$year_field];
		} else if ($has_day && $has_month && ! $has_year) {
			return $month . ' ' . $date[$day_field];
		} else if ($has_day && ! $has_month && $has_year) {
			return '[Unknown Month] ' . $date[$day_field] . ', ' . $date[$year_field];
		} else if ( ! $has_day && $has_month && $has_year) {
			return $month . ' ' . $date[$year_field];
		} else if ( ! $has_day && ! $has_month && $has_year) {
			return $date[$year_field];
		} else if ( ! $has_day && $has_month && ! $has_year) {
			return $month;
		} else if ($has_day && ! $has_month && ! $has_year) {
			return '[Unknown Month] ' . $date[$day_field];
		}

		return NULL;
	} // function multi_col_date_format
} // class