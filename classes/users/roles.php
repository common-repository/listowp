<?php

class Listo_User {

    public static function is_admin() {

        static $is_admin = NULL;

        if (NULL !== $is_admin) {
            return $is_admin;
        }

        // WP admins
        if (current_user_can('manage_options')) {
            return $is_admin = TRUE;
        }


        if (!get_current_user_id()) {
            return $is_admin = FALSE;
        }

        return $is_admin = FALSE;
    }

	public static function get_id() {
		if($id = get_current_user_id()) {
			return $id;
		}

		if(apply_filters('listowp_demo_mode', FALSE)) {
			if ( $id = ( Listo_Demo::get_instance() )->get_id() ) {
				return $id;
			}
		}

		if(apply_filters('listowp_supercookie', FALSE)) {
			return 1;
		}

        return FALSE;
	}


}