<?php

class Listo_User_Preferences {


	private static $instance = NULL;
	private $prefs = [];
	public static function get_instance() {
		return (NULL==self::$instance) ? new self() : self::$instance;
	}

	private function __construct() {
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}listo_user_preferences WHERE `user`=%d", Listo_User::get_id());
		$rows = $wpdb->get_results( $sql, ARRAY_A );

		if ( $wpdb->num_rows > 0 ) {
			foreach ( $rows as $row ) {
				$this->prefs[$row['name']] = maybe_unserialize($row['value']);
			}
		}
	}

	public function set($name, $value) {
		global $wpdb;

		$value = maybe_serialize($value);

		// Insert
		if(!isset($this->prefs[$name])) {
			$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}listo_user_preferences (`user`,`name`,`value`) VALUES (%d, %s, %s)", Listo_User::get_id(), $name, $value);
			//new Listo_Debug($sql);
			$wpdb->query($sql);
		}

		// Update
		$sql = $wpdb->prepare("UPDATE {$wpdb->prefix}listo_user_preferences SET `value`=%s WHERE `user`=%d AND `name`=%s", $value, Listo_User::get_id(), $name);
		$wpdb->query($sql);
		//new Listo_Debug($sql);
		$this->prefs[$name]=$value;
	}

	public function get($name, $default = NULL) {

		if(isset($this->prefs[$name])) {
			return $this->prefs[$name];
		}

		return $default;
	}

}