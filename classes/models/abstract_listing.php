<?php
/*
 * Model for listing items
 */
abstract class Listo_Model_List {

    public $id;
    public $author;

	protected $model = '';
    protected $request;
    protected $wpdb;
    protected $query;
    protected $table;

    /** INIT **/

    public function __construct(WP_REST_Request $request) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->request = $request;
    }

	protected function init($model) {
		$this->model = 'Listo_model_'.$model;
		$this->table = $this->model::config_table();
	}

    /** CREATE **/

    /** READ **/
    protected function prepare_result($data) {
        if(count($data)) {
            foreach($data as $key=>$id) {
                $model = new $this->model($id);
                $data[$key] = $model->get_item();
            }
            return $data;
        }

        return [];
    }

    protected function query_init($where=[], $order=[]) {
        $this->query_start();
        $this->query_where($where);
        $this->query_order($order);
    }

    protected function query_start() {
        $this->query= "SELECT id FROM {$this->table}";
    }

    // Add shared WHERE statements
    protected function query_where($where=[]) {
        global $wpdb;

        if($this->request['id']) {
            $where['id'] = $wpdb->prepare('id=%d',$this->request['id']);
        }


        // By default users can see only their own entries
        // @todo This will need to be expanded once we have third party assignees, task/list sharing etc
	    if(!defined('LISTOWP_NOPRIV_MODE')) {
		    $where['author'] = $wpdb->prepare( 'author=%d', Listo_User::get_id() );
	    }

	    if(count($where)) {
            $where = " WHERE " . implode(' AND ', $where);
        }
//print_r($where);
        $this->query .= is_string($where) ? $where : FALSE;
    }

    // Add default ORDER statement
    protected function query_order($order=[], $use_defaults = TRUE) {
		if($use_defaults) {
			$order = array_merge( $order, [ 'order' => '`order` ASC', 'id' => '`id` DESC' ] );
		}

		foreach($order as $k=>$v) {
			if(NULL==$v) {
				unset($order[$k]);
			}
		}
        $this->query .= " ORDER BY ".implode(',', $order);
//		new Listo_Debug($this->query);
    }

    /** UPDATE **/
    public function reorder(){
        if(count($this->request['ids'])) {
            $order = 1;
            foreach($this->request['ids'] as $id) {
                $model = new $this->model($id);
	            $model->set('order', $order);
                $order++;
            }
        }
    }

    /** DELETE **/



}