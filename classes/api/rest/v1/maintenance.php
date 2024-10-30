<?php

class Listo_REST_V1_Endpoint_Maintenance extends Listo_REST_V1_Endpoint {

	public function read(WP_REST_Request $request) {

		global $wpdb;
		$res = [];
		$res[] = ['id'=>'inbox'];
		$res[] = ['id'=>'scheduled'];
		$res[] = ['id'=>'all'];
		$res[] = ['id'=>'due'];
		$res[] = ['id'=>'done'];

		$res = apply_filters('listowp_maintenance_smart_collections', $res);


		if(!defined('LISTOWP_NOPRIV_MODE')) {
			$sql = "SELECT id FROM {$wpdb->prefix}listo_collections WHERE author=" . Listo_User::get_id();
		} else {
			$sql = "SELECT id FROM {$wpdb->prefix}listo_collections";
		}

		$cols = $wpdb->get_results($sql, ARRAY_A);

		$res = array_merge($res, $cols);

		$response = [];
		// Collection specific counts
		if(count($res)) {
			foreach($res as $collection) {
				$id = $collection['id'];

				if(is_numeric($id)) {
					$sql_base = "SELECT COUNT(id) as count FROM {$wpdb->prefix}listo_items WHERE collection=$id AND";
				} else {
					if('inbox' == $id) {
						$sql_base = "SELECT COUNT(id) as count FROM {$wpdb->prefix}listo_items WHERE collection IS NULL AND";
					} elseif('scheduled' == $id) {
						$sql_base = "SELECT COUNT(id) as count FROM {$wpdb->prefix}listo_items WHERE due IS NOT NULL AND";
					} elseif('all' == $id) {
						$sql_base = "SELECT COUNT(id) as count FROM {$wpdb->prefix}listo_items WHERE status=0 AND";
					} elseif('due' == $id) {
						$sql_base= $wpdb->prepare("SELECT COUNT(id) as count FROM {$wpdb->prefix}listo_items WHERE due <=%s AND",current_time('mysql',TRUE));
					} elseif('done' == $id) {
						$sql_base= $wpdb->prepare("SELECT COUNT(id) as count FROM {$wpdb->prefix}listo_items WHERE status=1 AND",current_time('mysql',TRUE));
					} else {
						$sql_base = apply_filters('listowp_maintenance_sql_base',  "SELECT COUNT(id) as count FROM {$wpdb->prefix}listo_items WHERE status=0 AND", $id);
					}
				}

				if(!defined('LISTOWP_NOPRIV_MODE')) {
					$sql_base .= " author=" . Listo_User::get_id() . " AND ";
				} else {
					$sql_base .= " ";
				}


				// All unfinished items
				$sql = $sql_base." status=0";
				$count = $wpdb->get_row($sql,ARRAY_A);
				$count_items = (int) $count['count'];
				$count_items_formatted = Listo_String::shorten_big_number($count_items,0,1000);

				// All finished items
				$sql = $sql_base." status=1";
				$count = $wpdb->get_row($sql,ARRAY_A);
				$count_items_done = (int) $count['count'];
				$count_items_done_formatted = Listo_String::shorten_big_number($count_items_done,0,1000);

				// All due items
				$sql = $wpdb->prepare($sql_base." status=0 AND due <= %s",current_time('mysql',TRUE));
				$count = $wpdb->get_row($sql,ARRAY_A);
				$count_items_due = (int) $count['count'];
				$count_items_due_formatted = Listo_String::shorten_big_number($count_items_due,0,1000);

				if('done'==$id) {
					$count_items = 0; // Done list has no open tasks by default
				}

				if('all'==$id) {
					$count_items_done = 0; // Open list has no done tasks by default
				}

				$update = compact('count_items_due','count_items','count_items_done');
				if(is_numeric($id)) {
					$wpdb->update( $wpdb->prefix . 'listo_collections', $update, [ 'id' => $id ] );
				} else {
					$meta = 'meta_'.$id;
					(Listo_User_Preferences::get_instance())->set($meta,$update);
				}

				$response[$id] = compact('count_items_due','count_items','count_items_done', 'count_items_due_formatted','count_items_formatted','count_items_done_formatted');
			}
		}

		// All items
		return [$response];
	}
}