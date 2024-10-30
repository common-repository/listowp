<?php

class Listo_REST_V1_Endpoint_Admin extends Listo_REST_V1_Endpoint {

    public function read(WP_REST_Request $request) {

		$position = $request['position'] ?? 'left';
        $config = Listo_Config::admin_config($position);

        return $config;
    }


    public function edit(WP_REST_Request $request) {
		// Values go through update_option, they are escaped there
        $value = $request['value'];
        $name = $request['name'];
        Listo_Config::set($name,$value);

        return array('success' => TRUE,'class'=>__CLASS__,'method'=>__METHOD__);
    }

    public function can($method) {
        return Listo_User::is_admin();
    }
}