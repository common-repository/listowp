<?php

class Listo_Config {
    public static function get($name, $default = FALSE, $check_length = FALSE) {
        $name = 'listowp_'.$name;
        $value = get_option($name);

        // Unset / empty settings return the default
        if(FALSE === $value) {
            return $default;
        }

        // Some empty strings (for example profile slugs) should return the default
        if(!strlen($value) && $check_length) {
            return $default;
        }

        return $value;
    }

    public static function set($name, $value) {
        $name = 'listowp_'.$name;
		$value = strip_tags($value);
        update_option($name, $value);
    }

    public static function admin_config($position='left') {

        $config = [
            'lists' => [
                'id'=>'lists',
                'title'=> __('Lists','listowp'),
                'icon' => 'fa fa-list-check',
                'description' => _x('Configure smart lists for all users','admin','listowp'),
                'items' => [
                    'collections_due_separator' => Listo_Config_Panel::admin_item('collections_due_separator', FALSE, Listo_Model_Collections::default_name('due')),
	                'collections_due_enabled' => Listo_Config_Panel::admin_item('collections_due_enabled'),
	                'collections_due_label' => Listo_Config_Panel::admin_item('collections_due_label', 'collections_due_enabled'),

	                'collections_scheduled_separator' => Listo_Config_Panel::admin_item('collections_scheduled_separator', FALSE, Listo_Model_Collections::default_name('scheduled')),
                    'collections_scheduled_enabled' => Listo_Config_Panel::admin_item('collections_scheduled_enabled'),
	                'collections_scheduled_label' => Listo_Config_Panel::admin_item('collections_scheduled_label', 'collections_scheduled_enabled'),

                    'collections_recurring_separator' => Listo_Config_Panel::admin_item('collections_recurring_separator', FALSE, Listo_Model_Collections::default_name('recurring')),
                    'collections_recurring_enabled' => Listo_Config_Panel::admin_item('collections_recurring_enabled'),

	                'collections_all_separator' => Listo_Config_Panel::admin_item('collections_all_separator', FALSE, Listo_Model_Collections::default_name('all')),
                    'collections_all_enabled' => Listo_Config_Panel::admin_item('collections_all_enabled'),
	                'collections_all_label' => Listo_Config_Panel::admin_item('collections_all_label', 'collections_all_enabled'),

	                'collections_done_separator' => Listo_Config_Panel::admin_item('collections_done_separator', FALSE, Listo_Model_Collections::default_name('done')),
                    'collections_done_enabled' => Listo_Config_Panel::admin_item('collections_done_enabled'),
	                'collections_done_label' => Listo_Config_Panel::admin_item('collections_done_label', 'collections_done_enabled'),

                    'collections_inbox_separator' => Listo_Config_Panel::admin_item('collections_inbox_separator', FALSE, Listo_Model_Collections::default_name('inbox')),
	                'collections_inbox_enabled' => Listo_Config_Panel::admin_item('collections_inbox_enabled'),
	                'collections_inbox_label' => Listo_Config_Panel::admin_item('collections_inbox_label'),
                ],
            ],
            'appearance' => [
	            'id'=>'appearance',
	            'title'=>_x('Appearance','admin','listowp'),
	            'icon' => 'fa fa-paintbrush',
	            'position' => 'right',
	            'items' => [

		            'style_separator' => Listo_Config_Panel::admin_item('style_separator',FALSE,_x('Style','admin','listowp')),
		            'default_theme_select' => Listo_Config_Panel::admin_item('default_theme_select',FALSE,_x('Default color scheme','admin','listowp'), ['options'=>apply_filters('listowp_theme_select',['light'=>_x('Light','Application color scheme', 'listowp'),'dark'=>_x('Dark','Application color scheme', 'listowp')])]),


		            'powered_by_separator' => Listo_Config_Panel::admin_item('powered_by_separator',FALSE,_x('Powered by ListoWP','admin','listowp')),
		            'powered_by_enabled' => Listo_Config_Panel::admin_item('powered_by_enabled'),
	            ],
            ],

            'datetime' => [
	            'id'=>'datetime',
	            'title'=>_x('Date & Time','admin','listowp'),
	            'icon' => 'fa fa-calendar-days',
	            'position' => 'right',
	            'items' => [
		            'df_separator' => Listo_Config_Panel::admin_item('df_separator',FALSE,_x('Date format','admin','listowp')),
		            'date_format' => Listo_Config_Panel::admin_item('date_format',FALSE,_x('Short','admin','listowp')),
		            'date_format_long' => Listo_Config_Panel::admin_item('date_format_long',FALSE,_x('Long','admin','listowp')),
	            ],
            ],

            'integrations' => [
                'id'=>'integrations',
                'title'=>_x('Integrations','admin','listowp'),
                'position' => 'right',
                'icon' => 'fa fa-code-compare',
                'items' => [

                    // PeepSo
                    'peepso_enabled_separator' => Listo_Config_Panel::admin_item('peepso_enabled_separator',FALSE,'PeepSo'),
                    'peepso_enabled' => Listo_Config_Panel::admin_item('peepso_enabled'),
                    'peepso_message' => Listo_Config_Panel::admin_item('peepso_message','peepso_enabled'),

                    // BuddyBoss
                    'bb_enabled_separator' => Listo_Config_Panel::admin_item('bb_enabled_separator',FALSE,'BuddyBoss'),
                    'bb_enabled' => Listo_Config_Panel::admin_item('bb_enabled'),

                    // WooCommerce
                    'woo_enabled_separator' => Listo_Config_Panel::admin_item('woo_enabled_separator',FALSE,'WooCommerce'),
                    'woo_enabled' => Listo_Config_Panel::admin_item('woo_enabled'),

                ],

            ],
            'advanced' => [
	            'id'=>'general',
	            'title'=>__('Advanced','listowp'),
	            'icon' => 'fa-solid fa-gears',
	            'position' => 'right',
	            'items' => [
		            'gdpr_enabled' => Listo_Config_Panel::admin_item('gdpr_enabled',FALSE,_x('Bulk export & delete (GDPR)','admin','listowp')),
		            'debug_enabled' => Listo_Config_Panel::admin_item('debug_enabled',FALSE,_x('Debug log','admin','listowp')),
	            ],
            ],

        ];


        $config = apply_filters('listowp_config',$config);

		if($position) {
			foreach($config as $group_key => $group) {
				$group_position = $group['position'] ?? 'left';
				if($group_position != $position) {
					unset($config[$group_key]);
				}
			}
		}

		return $config;
    }
}
