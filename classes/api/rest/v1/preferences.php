<?php

class Listo_REST_V1_Endpoint_Preferences extends Listo_REST_V1_Endpoint {

	private $preferences = [
		'timezone',
		'items_order',
		'items_order_dir',
		'items_hide_done',
	];

	private $defaults = [
		'items_hide_done'  => 1,
		'timezone'         => NULL,
	];

	#69 - there are no params that require permissions checks
    public function read(WP_REST_Request $request) {

		$this->defaults['timezone'] = Listo_Time::current();
		$keys = $this->preferences;

		if($request['id']) {
			$keys=[sanitize_key($request['id'])];
		}

        $preferences = [];

	    $Listo_User_Preferences = Listo_User_Preferences::get_instance();

		foreach($keys as $key) {
			$default = $this->defaults[$key] ?? NULL;

			$value = $Listo_User_Preferences->get($key);

			$preferences[$key] = strlen($value) ? $value : $default;
		}


        return $preferences;
    }


	#69 - there are no params that require permissions checks
    public function edit(WP_REST_Request $request) {
	    (Listo_User_Preferences::get_instance())->set(sanitize_key($request['id']), $request['value']);
        return array('success' => TRUE,'class'=>__CLASS__,'method'=>__METHOD__);
    }
}
