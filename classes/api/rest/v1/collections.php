<?php

class Listo_REST_V1_Endpoint_Collections extends Listo_REST_V1_Endpoint {

    public function __construct()
    {
        parent::__construct();
        //$this->configure_link('author','users','author',TRUE);
    }

    public function create(WP_REST_Request $request) {
        $collection = new Listo_Model_Collection();
		$request['id'] = $collection->id;

	    return $this->read($request);
    }

    public function read(WP_REST_Request $request) {

        $this->model = new Listo_Model_Collections($request);
        $this->init($request);

        $this->items = $this->model->read();

        return $this->items_to_response();
    }


    public function edit(WP_REST_Request $request) {

        if($request['id']) {
            // Editing one instance
            $this->model = new Listo_Model_Collection($request['id']);

            $properties = $this->model->editable_properties;

            foreach($properties as $property) {
                if(isset($request[$property])) { $this->model->set($property, $request[$property]); }
            }

            return array('success' => TRUE,'class'=>__CLASS__,'method'=>__METHOD__.'_modify');

        } elseif($request['ids']) {
            $this->model = new Listo_Model_Collections($request);
            $this->model->reorder();
            return array('success' => TRUE,'class'=>__CLASS__,'method'=>__METHOD__.'_reorder');
        }
	    return $this->read($request);
    }

	public function delete(WP_REST_Request $request) {
		if($request['id'] && $this->model = new Listo_Model_Collection($request['id'])) {
			$this->model->delete($request);
		}

		return array('success' => TRUE,'class'=>__CLASS__,'method'=>__METHOD__);
	}
}