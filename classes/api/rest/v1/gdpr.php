<?php

class Listo_REST_V1_Endpoint_GDPR extends Listo_REST_V1_Endpoint {
    public function delete(WP_REST_Request $request) {
		global $wpdb;

	    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}listo_collections where author=%d",Listo_User::get_id()));
	    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}listo_items where author=%d",Listo_User::get_id()));
	    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}listo_user_preferences where user=%d",Listo_User::get_id()));
        return array('success' => TRUE,'class'=>__CLASS__,'method'=>__METHOD__);
    }
}
