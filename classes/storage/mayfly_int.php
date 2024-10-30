<?php



if(!class_exists('Listo_Mayfly')) {
    require_once(dirname(__FILE__) . '/mayfly.php');
}

/**
 * Class Listo_Mayfly_Int
 *
 * This class is intended to be an interface to integra values in the Mayfly storage
 * It allows increment and decrement operations without race conditions
 *
 * The increment() and decrement() methods by default don't modify the TTL of the record unless $reset_ttl is provided
 */
class Listo_Mayfly_Int extends Listo_Mayfly {

	/**
	 * Creates a new entry of type int - use this if you intend to use increment / decrement later
	 * @param string $name - Duplicates will be overwritten. Characters, underscores, numbers only.
	 * @param int - Must be an int
	 * @param int $ttl - Time To Live in seconds. Default -1 = infinity (9999-09-09)
	 */
	public static function set(string $name, $value, int $ttl = -1) {
		if(!is_int($value)) {
			trigger_error( 'mayfly::'.__FUNCTION__ .': Value must be an int - ' . maybe_serialize($value), E_USER_ERROR);
		}
        $value = intval($value);

		parent::set($name, $value, $ttl);
	}

	public static function increment(string $name, int $amount = 1, $reset_ttl = FALSE) {
		return self::change($name, $amount, '+', $reset_ttl);
	}

	public static function decrement(string $name, int $amount = 1, $reset_ttl = FALSE) {
		return self::change($name, $amount, '-', $reset_ttl);
	}

	private static function change(string $name, int $amount = 1, $direction='+', $reset_ttl = FALSE) {

		global $wpdb;

		$name = sanitize_key($name);

		// Don't let increment/decrement to happen on non-int values
		$old_value = self::get($name);
		if(!is_numeric($old_value)) {

			// are we incrementing a non-existent entry?
			if(NULL == $old_value) {

				$ttl = $reset_ttl ? $reset_ttl : -1;

				self::set($name, 0, $ttl);
			} else {
				trigger_error( 'mayfly_int::' . __FUNCTION__ . "($name$direction$amount): Old value is not an integer - " . maybe_serialize( $old_value ), E_USER_ERROR );
			}
		}

		$query = "UPDATE {$wpdb->prefix}listo_mayfly SET `value` = `value` $direction $amount";

		if(FALSE !== $reset_ttl) {

			$query .= ", `expires` = ";

			if( -1 == $reset_ttl) {
				$query .= " '9999-09-09 09:09:09' ";
			} else {
				$query .= " DATE_ADD((select NOW()), INTERVAL $reset_ttl second) ";
			}
		}
		$query .=" WHERE `name` = '$name'";

		if($count = $wpdb->query($query)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}