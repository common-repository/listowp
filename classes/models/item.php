<?php

class Listo_Model_Item extends Listo_Model_Single {

	public $collection;
	public $status;
	public $due;
	public $due_formatted;
	public $due_formatted_long;
	public $due_formatted_long_time;
	public $due_past;

	public $due_this_year;

	public $rrule = NULL;
	public $rrule_id = NULL;

	public $rrule_label = NULL;


	public function __construct($id = NULL)
	{
		$this->editable_properties = ['title','description','status','order','collection','due','edited','closed','rrule','rrule_id'];

		parent::__construct($id);
	}

	public function set($property, $value) {
		if ( 'due' == $property ) {
			if(strlen($value)) {
				$value = Listo_Time::_( $value );
			} else {
				$value = NULL;

				// #117 automatically wipe RRULE if due date is removed
				$this->set('rrule', NULL);
				$this->set('rrule_id', NULL);
			}
		}

		if('rrule_id' == $property) {
			if('none' == $value) {
				$value = NULL;
			}
		}

		if('rrule' == $property) {
			if('' == $value) {
				$value = NULL;
			}
		}

		if ( 'status' == $property) {
			if(1==$value) {
				$now = current_time('mysql',TRUE);
				$this->set('closed', $now);
			} else {
				$this->set('closed', NULL);
			}
		}

		do_action('listowp_item_property_set_'.$property, $value, $this);

		parent::set( $property, $value );
	}

	public static function config_table() {
		global $wpdb;

		return $wpdb->prefix.'listo_items';
	}

	public function get_item($return = ARRAY_A)
	{
		if ( null != $this->due ) {
			$this->due = Listo_Time::_( $this->due, false );

			$this->due_formatted = Listo_Time::format($this->due);
			$this->due_formatted_long = Listo_Time::format($this->due, TRUE);
			$this->due_formatted_long_time = Listo_Time::format($this->due, TRUE, TRUE);

			$diff = Listo_Time::diff_human($this->due);
			$this->due_past = $diff['past'];
			$this->due_this_year = $diff['this_year'];

			if($this->rrule_id && class_exists('Listo_Recurring')) {
				$defaults = Listo_Recurring::defaults(date('Y-m-d H:i:s'));
				if(array_key_exists($this->rrule_id, $defaults)) {
					$this->rrule_label = $defaults[$this->rrule_id]['label'];
				}
			}
		}

		return parent::get_item( $return );
	}
}