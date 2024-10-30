<?php

class Listo_Activation {

    public static function _() {

		@mkdir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'listowp');
		if(!get_option('listowp_debug_file')) {
			$file = md5(microtime().$_SERVER['HTTP_HOST']);
			update_option('listowp_debug_file', $file);
		}
	    $fh = fopen(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'listowp'.DIRECTORY_SEPARATOR.get_option('listowp_debug_file').'.log', 'w');
	    fclose($fh);
	    $fh = fopen(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'listowp'.DIRECTORY_SEPARATOR.'index.html', 'w');
	    fclose($fh);

        $config = [
            'collections_enabled' => 1,
            'collections_due_enabled' => 1,
            'collections_scheduled_enabled' => 1,
            'collections_recurring_enabled' => 0, // Pro only
            'collections_all_enabled' => 1,
            'collections_done_enabled' => 1,
            'powered_by_enabled'=>0,
            'date_format' => 'M j',
            'date_format_long' => 'M j, Y',
            'default_theme_select' => 'light',
        ];

        foreach($config as $name=>$value) {
            Listo_Config::set($name, $value);
        }

        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $charset = $wpdb->get_charset_collate();

        ob_start();

	    if(NULL === get_option('listowp_install_date', NULL)) {
			update_option('listowp_install_date', date('Y-m-d H:i:s'));
	    }

        $sql = "CREATE TABLE " . $wpdb->prefix . "listo_items (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `collection` bigint(20) NULL,
            `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0, 
            `order` bigint(20) UNSIGNED NOT NULL DEFAULT 0,   
            `author` int(11) UNSIGNED NOT NULL, 
			`title` varchar(255) NOT NULL DEFAULT '',
			`description` TEXT NOT NULL DEFAULT '',
			`due` DATETIME DEFAULT NULL,
			`rrule` VARCHAR(256) DEFAULT NULL,
			`rrule_id` VARCHAR(16) DEFAULT NULL,
			`created` DATETIME DEFAULT NULL,
			`closed` DATETIME DEFAULT NULL,
			`edited` DATETIME DEFAULT NULL,
			PRIMARY KEY (id)
		) $charset;";
        dbDelta($sql);


	    $sql = "CREATE TABLE " . $wpdb->prefix . "listo_collections (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
            `order` bigint(20) UNSIGNED NOT NULL DEFAULT 0, 
            `smart` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
            `author` int(11) UNSIGNED NOT NULL, 
			`title` varchar(255) NOT NULL DEFAULT '',
			`description` TEXT NOT NULL DEFAULT '',
			`icon` varchar(48) NOT NULL DEFAULT '',
			`color` varchar(24) NOT NULL DEFAULT '',
			`created` DATETIME DEFAULT NULL,
			`closed` DATETIME DEFAULT NULL,
			`edited` DATETIME DEFAULT NULL,
			`count_items` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			`count_items_done` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			`count_items_due` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (id)
		) $charset;";
	    dbDelta($sql);

	    $sql = "CREATE TABLE " . $wpdb->prefix . "listo_user_preferences (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `user` bigint(20) UNSIGNED NOT NULL,
            `key` VARCHAR(64), 
            `last_activity` DATETIME DEFAULT NULL,
            `value` VARCHAR(256),
			PRIMARY KEY (id)
		) $charset;";
	    dbDelta($sql);

	    if(defined('LISTOWP_DEMO_MODE')) {
		    $sql = "CREATE TABLE " . $wpdb->prefix . "listo_demo (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `last_activity` DATETIME DEFAULT NULL,
            `secret` VARCHAR(64) NOT NULL,
			PRIMARY KEY (id)
		) $charset;";
		    dbDelta($sql);

			if(!get_option('listowp_did_auto_increment')) {
				$sql = "ALTER TABLE {$wpdb->prefix}listo_demo AUTO_INCREMENT=1000000000";
				$wpdb->query($sql);
				update_option('listowp_did_auto_increment',1);
			}
	    }

	    $sql = "CREATE TABLE {$wpdb->prefix}listo_mayfly (
					`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
					`name` VARCHAR(256),
					`value` MEDIUMTEXT NULL,
					`expires` DATETIME DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (id),
					UNIQUE INDEX id (id),
					INDEX name (name)
				) ENGINE=InnoDB";
	    dbDelta($sql);
        new Listo_Debug(ob_get_clean());
    }
}