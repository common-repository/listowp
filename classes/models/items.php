<?php

class Listo_Model_Items extends Listo_Model_List {

    public function __construct(WP_REST_Request $request) {
        parent::__construct($request);
    }

    public function read()
    {
	    $this->init('item');
        $this->query_start();

		$prefs = Listo_User_Preferences::get_instance();

		$where = [];

	    if($this->request['collection']) {

		    if (is_numeric($this->request['collection']) && $this->request['collection'] > 0) {
			    // Specific collection
			    $where['collection'] = $this->wpdb->prepare('collection=%d', $this->request['collection']);
		    } elseif ($this->request['collection'] == 'inbox') {
			    // Inbox - items without a collection
			    $where['collection'] = 'collection IS NULL';
		    } elseif($this->request['collection']=='due') {
				$due = current_time('mysql',TRUE);
			    $where['due'] = '`due` <= \''.$due.'\'';
		    } elseif($this->request['collection']=='scheduled') {
			    $where['due'] = '`due` IS NOT NULL';
		    } elseif($this->request['collection']=='done') {
			    $where['status'] = '`status`=1';
		    } elseif($this->request['collection']=='all') {
			    $where['status'] = '`status`=0';
		    }
	    }

		// Hide "Done" except "done" smart list
	    if($prefs->get('items_hide_done', 1) && $this->request['collection']!='done') {
		    $where['status'] = '`status`=0';
	    }

		$where = apply_filters('listowp_items_query_where', $where, $this->request);

		$this->query_where($where);

		// Ordering
		$order = [];

	    /**
	     * order        `order` - no  ASC or DESC here
	     * status       `status`
	     * due          `due`, but keep empty on the bottom
	     */

	    $sort = (string) $prefs->get('items_order', 0);
	    $sort_dir = $prefs->get('items_order_dir', 'asc');
		new Listo_Debug($sort);
		switch($sort) {
			case 'due':
				$order['due_is_null'] = '(`due` IS NULL) ASC'; # https://stackoverflow.com/questions/8510632/php-mysql-order-by-date-but-empty-dates-last-not-first
				$order['due'] = '`due` '.$sort_dir;
				break;
			case 'status':
				$order['status'] = '`status` '.$sort_dir;
				break;
			case 'edited':
				$order['edited'] = '`edited` '.$sort_dir;
				break;
			case 'created':
				$order['created'] = '`created` '.$sort_dir;
				break;
			default:
				// Abstract knows what to do
				break;
		}



		$this->query_order($order);

        $ids = [];
        $result = $this->wpdb->get_results($this->query, ARRAY_A);
        foreach($result as $id) {
            $ids[]=$id['id'];
        }
        $data = $this->prepare_result($ids);

        return $data;
    }

    public function reorder()
    {
        $this->init('item');
        parent::reorder();
    }

	public function after_collection_delete($collection, $action = 'delete') {
		global $wpdb;
		// This will eventually be "move to another list"
//		if('inbox' == $action) {
//			$sql = $wpdb->prepare("UPDATE {$wpdb->prefix}listo_items SET collection=%d WHERE collection=%d AND author=%d", 0, $collection, Listo_User::get_id());
//		}

		if(!defined('LISTOWP_NOPRIV_MODE')) {
			$sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}listo_items WHERE collection=%d AND author=%d", $collection, Listo_User::get_id() );
		} else {
			$sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}listo_items WHERE collection=%d", $collection );
		}


		$wpdb->query($sql);
	}
}
