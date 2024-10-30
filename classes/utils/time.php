<?php

class Listo_Time {

	public static function _($datetime, $to_gmt = TRUE) {

		$timezone_gmt = new DateTimeZone('GMT');

		$timezone = (Listo_User_Preferences::get_instance())->get('timezone',);

		if(strlen($timezone)) {

			$options = self::options();

			if(in_array($timezone, $options)) {
				$timezone = new DateTimeZone( $timezone );
			}
		}

		if(!$timezone instanceof  DateTimeZone) {
			$timezone = wp_timezone();
		}

		$timezone_from = $timezone;
		$timezone_to = $timezone_gmt;

		if(!$to_gmt) {
			$timezone_from = $timezone_gmt;
			$timezone_to = $timezone;
		}

		$datetime_obj = new DateTime( $datetime, $timezone_from);
		$datetime_obj->setTimezone($timezone_to);

		return $datetime_obj->format( 'Y-m-d H:i:s' );
	}

	public static function format($date, $long=FALSE) {
		if(!is_int($date)) {
			$date = strtotime($date);
		}

		$config = $long ? 'date_format_long' : 'date_format';

		return date_i18n( Listo_Config::get($config),$date);
	}

	public static function diff_human($date) {
		$response = [
			'diff' => '',
			'past' => 0,
			'this_year' => 0,
		];

		if(!is_int($date)) {
			$date = strtotime( $date );
		}
		$now = strtotime(Listo_Time::_(current_time('mysql',TRUE), FALSE));

		$ynow = date('Y',$now);
		$ydate = date('Y', $date);
		if($ynow==$ydate) { $response['this_year'] = 1; }
		// Days

		$difference = floor(($now - $date)/3600/24);

		if ($difference >= 0) {
			$response['past'] = 1;
		}

		return $response;
	}

	public static function current() {
		$timezone = (Listo_User_Preferences::get_instance())->get('timezone');

		if($timezone) {
			$options = self::options();
			if(in_array($timezone, $options)) {
				return $timezone;
			}
		}

		return self::offset_to_string( get_option( 'gmt_offset' ));

	}
	public static function options() {

		$offset_range = [-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
			0, 0.5, 1.00, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14];

		$options = [];

		foreach ($offset_range as $offset) {
			$options[] = self::offset_to_string( $offset);;
		}

		return $options;

	}

	private static function offset_to_string($offset) {
		$offset  = (float) $offset;
		$hours   = (int) $offset;
		$minutes = ( $offset - $hours );

		$sign      = ( $offset < 0 ) ? '-' : '+';
		$abs_hour  = abs( $hours );
		$abs_mins  = abs( $minutes * 60 );
		return Listo_String::format( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
	}
}
