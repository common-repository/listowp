<?php
/**
 * Plugin Name: ListoWP
 * Description: Front-end To Do Lists for your WordPress users
 * Author: EmeraldWP
 * Author URI: https://ListoWP.com
 * Version: 1.0.3
 * Copyright: (c) EmeraldWP
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: listowp
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 *
 * Special thanks:
 *
 * Randy Miller for the general idea of a modern "to do" plugin for WordPress
 * PeepSo, Inc. for the PeepSo (R) framework.
 **/

if(class_exists('Listo_Plugin')) {
	add_action('admin_init', function() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action('admin_notices', function() {
			$error_msg = 'Please make sure to have only one version of ListoWP active. If you are using ListoWP Pro, the free version can be deactivated and removed.';
			echo '<div class="error listo">' . $error_msg . '</div>';
		});
	});
} else {

	class Listo_Plugin {

		private static $instance = null;

		const PLUGIN_VERSION = '1.0.3';

		public static function get_instance() {
			return ( null == self::$instance ) ? new self() : self::$instance;
		}


		private function __construct() {
			$this->autoload();
			add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
		}


		/** UTILITIES **/

		private function autoload() {
			require_once( 'classes' . DIRECTORY_SEPARATOR . 'boot' . DIRECTORY_SEPARATOR . 'all.php' );
			Listo_Autoload::get_instance();
			Listo_API::get_instance();
			Listo_Frontend_Assets::get_instance();

			register_activation_hook( __FILE__, [ 'Listo_Activation', '_' ] );
			if ( class_exists( 'Listo_Activation_Pro' ) ) {
				register_activation_hook( __FILE__, [ 'Listo_Activation_Pro', '_' ] );
			}
		}

		public function load_textdomain() {
			$path = str_ireplace( WP_PLUGIN_DIR, '', dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
			load_plugin_textdomain( 'listowp', false, $path );
		}
	}

	Listo_Plugin::get_instance();
}
