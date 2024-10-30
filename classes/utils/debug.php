<?php


class Listo_Debug {
	public function __construct( $msg ) {
		if ( !Listo_Config::get('debug_enabled')) {
			return ( false );
		}

		$msg = is_string($msg) ? strip_tags($msg) : $msg;
		//$msg = maybe_serialize( $msg );

		$msg .= "\n";

		$msg = current_time( 'Y-m-d H:i:s ', 1 ) . $msg;
		error_log( "\n" . $msg, 3, WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'listowp' . DIRECTORY_SEPARATOR . get_option('listowp_debug_file').'.log' );
	}
}

// EOF