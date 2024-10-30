<?php

class Listo_Model_Collection extends Listo_Model_Single {

    public $smart = FALSE;
    public $icon;
    public $color;

	public $count_items_done;
	public $count_items;
	public $count_items_due;

	public $count_items_done_formatted;
	public $count_items_formatted;
	public $count_items_due_formatted;

    public function __construct($id = NULL)
    {
        $this->editable_properties = ['title','description','order','icon','color'];

        parent::__construct($id);
    }

    public static function config_table() {
        global $wpdb;

        return $wpdb->prefix.'listo_collections';
    }

    public function get_item($return = ARRAY_A) {

        if(is_numeric($this->id) && $this->id > 0) {

            $item = parent::get_item($return);

            // #40. Initials will always be provided for fallback when icon is deleted.
            if(true || !strlen($item['icon'])) {
	            // $item['icon'] = '';

				$title = strip_tags($item['title']);

				// Remove non-UTL chars
	            $title = preg_replace('/[\x00-\x1F\x7F]/u', '', $title);

				// Make sure commas, parentheses etc don't interfere
				$title = str_replace(['"', '\'',',','.','-','+','&','_','=',':',';','/','\\','%','$','#','@','!','(',')','*','[',']','{','}','+','.',',','?'],' ', $title);

				// Remove double spaces
	            $title = preg_replace('/\s+/', ' ', $title);

				// Remove spaces from beginning and end
				$title = trim($title);

	            if(stristr($title, " ")) {
					$initials = explode(" ", $title);
					foreach($initials as &$initial) {
						$initial = trim($initial);
					}
					$initials = mb_substr($initials[0],0,1). mb_substr($initials[1],0,1);
				} else {
					$initials = mb_substr($title,0,2);
				}
	            $item['initials'] = mb_strtoupper($initials);
            }

            if(!strlen($item['color'])) {
                $item['color'] = Listo_Model_Collections::default_color('custom');
            }

	        $item['count_items_due_formatted'] = Listo_String::shorten_big_number($item['count_items_due'],0,1000);
	        $item['count_items_formatted'] = Listo_String::shorten_big_number($item['count_items'],0,1000);
	        $item['count_items_done_formatted'] = Listo_String::shorten_big_number($item['count_items_done'],0,1000);

            return $item;

        } else {
			$Listo_User_Preferences = Listo_User_Preferences::get_instance();

            $this->author=Listo_User::get_id();
	        $this->count_items = $this->count_items_done = $this->count_items_due = 0;

	        $meta = 'meta_'.$this->id;
	        $meta = $Listo_User_Preferences->get($meta);
	        if(is_array($meta)) {
		        foreach($meta as $key=>$value) {
					if(defined('LISTOWP_BIG_LABELS')) { $value = $value*1000; }
			        $this->$key = $value;
			        $key_formatted = $key.'_formatted';
			        $this->$key_formatted = Listo_String::shorten_big_number($value,0,1000);
		        }
			}

            if($this->id == 'due') {
                $this->smart = TRUE;
                $this->title = Listo_Model_Collections::name('due');
                $this->description = __('All due tasks','listowp');
                $this->order = -100;
                $this->icon = Listo_Model_Collections::icon('due');
            }

            if($this->id == 'scheduled') {
                $this->smart = TRUE;
                $this->title = Listo_Model_Collections::name('scheduled');
                $this->description = __('All tasks with a due date','listowp');
                $this->order = -80;
                $this->icon = Listo_Model_Collections::icon('scheduled');
            }

	        if($this->id == 'all') {
		        $this->smart = TRUE;
		        $this->title = Listo_Model_Collections::name('all');
		        $this->description = __('All uncompleted tasks','listowp');
		        $this->order = -60;
		        $this->icon = Listo_Model_Collections::icon('all');
	        }

	        if($this->id == 'done') {
		        $this->smart = TRUE;
		        $this->title = Listo_Model_Collections::name('done');
		        $this->description = __('All completed tasks','listowp');
		        $this->order = -40;
		        $this->icon = Listo_Model_Collections::icon('done');
	        }

	        if($this->id == 'inbox') {
		        $this->smart = TRUE;
		        $this->title = Listo_Model_Collections::name('inbox');
		        $this->description = __('Tasks not assigned to a list','listowp');
		        $this->order = -20;
		        $this->icon = Listo_Model_Collections::icon('inbox');
	        }

	        if($this->id == 'recurring') {
		        $this->smart = TRUE;
		        $this->title = Listo_Model_Collections::name('recurring');
		        $this->description = __('All repeating tasks','listowp');
		        $this->order = -70;
		        $this->icon = Listo_Model_Collections::icon('recurring');
	        }

            return parent::get_item($return);
        }
    }

	public function delete($request=NULL) {
		parent::delete();

		$action = 'delete';
//		if($request && isset($request['cleanup'])) {
//			$action = $request['cleanup'];
//		}
//
//		if(!in_array($request['cleanup'],['inbox','delete'])) {
//			$action = 'inbox';
//		}

		$model = new Listo_Model_Items($request);
		$model->after_collection_delete($this->id, $action);

//		if(class_exists('Listo_Model_Labels')) {
//			$model = new Listo_Model_Labels($request);
//			$model->after_collection_delete($this->id, $action);
//		}
	}
}
