<?php

class Listo_GDPR {

	private static $instance;

	public static function get_instance()
	{
		return isset(self::$instance) ? self::$instance : self::$instance = new self;
	}

	private function __construct() {
		add_action('template_redirect',function(){
			if(isset($_GET['listowp_gdpr_export'])) {
				$export = [];

				$request = new WP_REST_Request();
				$request->set_method('GET');

				$Listo_Model_Collections = new Listo_Model_Collections($request);
				$export['lists'] = $Listo_Model_Collections->read();

				$Listo_Model_Items = new Listo_Model_Items($request);
				$export['tasks'] = $Listo_Model_Items->read();

				header('Content-disposition: attachment; filename=lists_task_export_'.time().'.json');
				header('Content-type: application/json');
				echo json_encode($export);
				die();
			}
		});
	}

}

Listo_GDPR::get_instance();

