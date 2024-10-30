<?php

class Listo_REST_V1_Endpoint_Items extends Listo_REST_V1_Endpoint {

    public function __construct()
    {
        parent::__construct();
//        $this->configure_link();
//        $this->configure_link('author','users','author',TRUE);

        if(Listo_Config::get('collections_enabled')) {
            //$this->configure_link('collection', 'collections', 'collection', TRUE);
        }
    }

    public function create(WP_REST_Request $request) {
        $item = new Listo_Model_Item();
	    $request['id'] = $item->id;

		return $this->read($request);
    }

    public function read(WP_REST_Request $request) {

        $this->model = new Listo_Model_Items($request);
        $this->init($request);

        $this->items = $this->model->read();

        if($request['collection']) {
            (Listo_User_Preferences::get_instance())->set('last_collection', $request['collection']);
        }

        return $this->items_to_response();
    }


    public function edit(WP_REST_Request $request) {

        if($request['id']) {
            // Editing one instance
            $this->model = new Listo_Model_Item($request['id']);
            $properties = $this->model->editable_properties;
            foreach($properties as $property) {
                if(isset($request[$property])) { $this->model->set($property, $request[$property]); }
            }
        } elseif($request['ids']) {
            // Reordering multiple instances
	        $this->model = new Listo_Model_Items( $request );
	        $this->model->reorder();
        }


	    return $this->read($request);
    }

    public function delete(WP_REST_Request $request) {

		$ids = [];

	    if($request['id']) {
		    $ids[] = $request['id'];
	    }  elseif($request['ids']) {
			$ids = $request['ids'];
	    }
		$errors=[];

		if(count($ids)) {
			foreach($ids as $id) {
				$id = (int) $id;
				$model = new Listo_Model_Item( $id );
				$model->delete();
			}
		} else {
			$errors[0] = 'No valid IDs';
		}

		if(count($errors)) {
			return array('success' => FALSE,'errors'=>$errors, 'class'=>__CLASS__,'method'=>__METHOD__);
		}

	    return array('success' => TRUE,'class'=>__CLASS__,'method'=>__METHOD__);
    }
}