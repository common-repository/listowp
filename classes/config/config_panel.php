<?php

class Listo_Config_Panel {
	private static $instance = NULL;

	public static function get_instance() {
		return (NULL==self::$instance) ? self::$instance = new self() : self::$instance;
	}

	private function __construct() {

		add_action('admin_menu', function() {

			add_menu_page('ListoWP', 'ListoWP',
				'manage_options',
				'listowp',
				array(&$this, 'dashboard'),
				'data:image/svg+xml;base64,' . 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgMTQ2Ni42NSAxNDQxLjQyIj4KICA8ZGVmcz4KICAgIDxzdHlsZT4uY2xzLTF7ZmlsbDp1cmwoI2xpc3RvZ3JhZGllbnQpO308L3N0eWxlPgogICAgPHJhZGlhbEdyYWRpZW50IGlkPSJsaXN0b2dyYWRpZW50IiBjeD0iMTE1OS42NCIgY3k9IjEwMi45OCIgcj0iMTU2Ny41OCIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiMwMGMyYWYiPjwvc3RvcD4KICAgICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMDBkZTdhIj48L3N0b3A+CiAgICA8L3JhZGlhbEdyYWRpZW50PgogIDwvZGVmcz4KICA8ZyBpZD0iQ2FwYV8yIiBkYXRhLW5hbWU9IkNhcGEgMiI+CiAgICA8ZyBpZD0iQ2FwYV8zMiIgZGF0YS1uYW1lPSJDYXBhIDMyIj4KICAgICAgPHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMTQ0MS41NCw1MzNBNzA2LjEsNzA2LjEsMCwwLDAsMTM5Mi4yMSw0MDRhNzEzLjYyLDcxMy42MiwwLDAsMC01Ni05My42Nyw3MjQuNjksNzI0LjY5LDAsMCwwLTg0LjI3LTk5LjE0QzExMTkuMTksODAuNjgsOTM1Ljg0LDAsNzMzLjMzLDAsMzI4LjI5LDAsMCwzMjIuNjUsMCw3MjAuNzRzMzI4LjI5LDcyMC42OCw3MzMuMzMsNzIwLjY4LDczMy4zMi0zMjIuNjUsNzMzLjMyLTcyMC42OEE3MTEuNDUsNzExLjQ1LDAsMCwwLDE0NDEuNTQsNTMzWk03MzMuMzMsMTE1OS4xN2MtMjQ4Ljc0LDAtNDUxLjA4LTE5Ni42OS00NTEuMDgtNDM4LjQzLDAtNS40Ny4xMi0xMC45NC4zNS0xNi4zNSwzLjM2LTg5LjI2LDM0LjI5LTE3MS43Niw4NC43NC0yMzkuNzMsODItMTEwLjQ0LDIxNS41MS0xODIuNDEsMzY2LTE4Mi40MSwxMDQuNzIsMCwyMDEuMjIsMzQuODcsMjc3Ljg0LDkzLjM4YTQ0Mi42NSw0NDIuNjUsMCwwLDEsOTEuNjcsOTRMNzQwLjkxLDcxNi44LDQ4OS4zLDU3Ni4zOGE2Mi44Nyw2Mi44NywwLDAsMC03MS41MSwxMDEuNzlMNjY2LDkyMi43M2ExMDEuMzYsMTAxLjM2LDAsMCwwLDEzNi40OCwyLjY0TDExNjcuNyw2MDIuNDlhNDI1LDQyNSwwLDAsMSwxNi43LDExOC4yNXEwLDIxLTIuMDYsNDEuNDZDMTE2MC44Miw5ODQuNTksOTY3LjU5LDExNTkuMTcsNzMzLjMzLDExNTkuMTdaIj48L3BhdGg+CiAgICA8L2c+CiAgPC9nPgo8L3N2Zz4K',
				3);

			add_submenu_page('listowp',esc_attr(_x('Configuration','admin','listowp')), esc_attr(_x('Configuration','admin','listowp')),
				'manage_options',
				'listowp',
				array(&$this, 'dashboard'),
				3);

			if(!apply_filters('listowp_is_pro', FALSE)) {
				add_submenu_page('listowp', _x('Upgrade to Pro!','admin','listowp'), _x('Upgrade to Pro!','admin','listowp'),
					'manage_options',
					'listowp-upgrade',
					['Listo_Config_Upgrade', 'dashboard'],
					2);
			}

			if(apply_filters('listowp_is_pro', FALSE)) {
				add_submenu_page('listowp', esc_attr(_x('License','admin','listowp')), esc_attr(_x('License','admin','listowp')),
					'manage_options',
					'listowp-license',
					[Listo_Config_License::get_instance(), 'dashboard'],
					2);
			}

		});

		add_action('admin_enqueue_scripts', function() {

			$assets_instance = Listo_Frontend_Assets::get_instance();
			$assets_url = $assets_instance->assets_url;
			$assets_dir = $assets_instance->assets_dir;
			wp_register_style('listowp-font', "https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap", [], Listo_Plugin::PLUGIN_VERSION, 'all');
			wp_register_style('listowp-fontawesome', "{$assets_url}css/icons.css", [], Listo_Plugin::PLUGIN_VERSION, 'all');
			wp_register_style('listowp-admin', "{$assets_url}css/admin.css", ['listowp-fontawesome', 'listowp-font'], Listo_Plugin::PLUGIN_VERSION.filemtime($assets_dir.'css/admin.css'), 'all');
			wp_register_style('listowp-admin-upgrade', "{$assets_url}css/admin-upgrade.css", ['listowp-fontawesome', 'listowp-font'], Listo_Plugin::PLUGIN_VERSION.filemtime($assets_dir.'css/admin-upgrade.css'), 'all');
			wp_register_script('listowp-admin', "{$assets_url}js/admin.js", ['jquery', 'underscore', 'wp-api-request'], Listo_Plugin::PLUGIN_VERSION.'.'.filemtime($assets_dir.'js/admin.js'), true);
			wp_localize_script('listowp-admin', 'listoAdminData', [
				'templates' => [
					'config_group'          => $assets_instance->get_template('admin/config_group'),
					'config_item_text'      => $assets_instance->get_template('admin/config_item_text'),
					'config_item_int'       => $assets_instance->get_template('admin/config_item_text'),
					'config_item_bool'      => $assets_instance->get_template('admin/config_item_bool'),
					'config_item_select'    => $assets_instance->get_template('admin/config_item_select'),
					'config_item_separator' => $assets_instance->get_template('admin/config_item_separator'),
					'config_item_message'   => $assets_instance->get_template('admin/config_item_message'),
				]
			]);
		});
	}

	public function dashboard() {
		wp_enqueue_style('listowp-admin');
		wp_enqueue_script('listowp-admin');
		?>
        <div class="loa-wrapper" id="listo_admin_container">
			<div class="loa-header">
				<img class="loa-header__logo" src="<?php echo Listo_Frontend_Assets::get_instance()->assets_url;?>images/logo_listowp.svg" />
			</div>
			<?php if(!apply_filters('listowp_is_pro', FALSE)) {
                // Content for promo codes is loaded from our server, no need for escaping
                //echo Listo_Remote_Content::get('upsell',TRUE,['location'=>'dashboard']);
            }?>
            <div class="loa-main" id="listo_admin_body_container">
                <div class="loa-column loa-column--left" id="listo_admin_body_left">
                    <span class="loa-loading loading">Loading...</span>
				</div>
				<div class="loa-column loa-column--right" id="listo_admin_body_right">
					<span class="loa-loading loading"></span>
				</div>
            </div>
        </div>
		<?php
	}

	public static function admin_item($name, $dep=FALSE, $label=FALSE, $args = []) {

		$config = [
        ];

		$config_name = $name;

		if(!array_key_exists($name, $config)) {

            // Date format
            if(substr($name, 0,11)=='date_format') {
                $config[$name] =[
		            'type' => 'text',
		            'title' => $label,
	            ];
            }

            // Select
			if(substr($name,-7,7) == '_select') {
				$config[$name]= [
					'type' => 'select',
					'options' => $args['options'],
					'title' => $label ? $label : __('Enabled','listowp'),
				];
			}

			// On-off switch
			if(substr($name,-8,8) == '_enabled') {
				$config[$name]= [
					'type' => 'bool',
					'title' => $label ? $label : __('Enabled','listowp'),
				];
			}

			// Separators
			if(substr($name,-10,10)=='_separator') {
				$config[$name]= [
					'type' => 'separator',
					'title' => $label ? $label : $name,
				];
			}

			// Slug
			if(stristr($name,'_slug')) {
				$config[$name] = [
					'type' => 'text',
					'title' => _x('Slug','admin','listowp'),
				];
			}

			// Label
			if(stristr($name,'_label')) {
				$config[$name] = [
					'type' => 'text',
					'title' => _x('Label','admin','listowp'),
				];
			}

			// Position
			if(stristr($name,'_position') || stristr($name,'_int')) {
                $label = $label ? $label : _x('Position','admin','listowp');
				$config[$name] = [
					'type' => 'int',
					'title' => $label,
				];
			}

			// Message
			if(stristr($name,'_message')) {
				$config[$name] = [
					'type' => 'message',
					'title' => FALSE,
				];
			}
		}

		if(!array_key_exists($name, $config)) {
			return [];
		}

		$item = $config[$name];
		$item['id'] = $name;

		$item['value'] = Listo_Config::get($config_name);
		if($dep) { $item['parent'] = $dep; }

		if($name == 'collections_inbox_enabled') {
			$item['value']=1;
			$item['readonly']=1;
		}

		if(!apply_filters('listowp_is_pro',0)) {
			if(stristr($name,'pro_') || stristr($name,'peepso_') || stristr($name, 'bb_') || stristr($name, 'woo_') || stristr($name,'recurring')) {
				if(!stristr($name,'separator')) {
					$item['pro'] = true;
					$item['value']=0;
					$item['readonly']=1;
				}
			}
		}


		$item['description'] = self::maybe_desc($item, $item['description'] ?? FALSE);

		return $item;
	}

	private static function maybe_desc($item, $default=FALSE) {
		$desc = [
			'peepso_enabled'=> class_exists('PeepSo') ? FALSE : _x('Requires the plugin "Community by PeepSo"','admin','listowp'),
			'peepso_message' => class_exists('PeepSo') ? Listo_String::format(_x('You can manage the PeepSo integration %s','admin','listowp'),'<a target="_blank" href="'.admin_url('admin.php?page=peepso_config&tab=listowp').'">'._x('here','admin','listowp').'</a>') : FALSE,
			'collections_inbox_enabled' => __('Inbox cannot be disabled','listowp'),
			'gdpr_enabled' => _x('Users will be able to bulk export and delete their lists and tasks in accordance to the privacy laws such as GDPR','admin','listowp'),
			'debug_enabled' => _x('Used only for debugging, recommended to keep disabled','admin','listowp'),
			'date_format' => _x('Used on Tasks due this year','admin','listowp'),
			'date_format_long' => _x('Used on Tasks due any other year','admin','listowp'),
            'bb_position' => self::maybe_desc_empty_for_default() .'<br/><b>By default, BuddyBoss does not number tabs from 1</b><br/>Please <a href="https://ListoWP.com/r/docs_config_integrations" target="_blank">check the documentation</a> for more information.'
		];



		if(isset($item['pro']) && !apply_filters('listowp_is_pro',0) ) {
            // @TODO #143 - convert upsell to static HTML included in the plugin
			$message = _x('Not configurable in the free version.','admin','listowp');
            $message .= '<a href="'.admin_url('admin.php?page=listowp-upgrade').'">'._x('Upgrade to Pro!','admin','listowp').'</a>';
            return $message;
//            return 'Not configurable in the free version. <a href="https://ListoWP.com/pricing" target="_blank">Upgrade to Pro!</a>';
		}

		if(isset($desc[$item['id']])) {
			return $desc[$item['id']];
		}

		if( stristr($item['id'],'_position') || stristr($item['id'],'_slug') || stristr($item['id'],'_label') || stristr($item['id'],'_int')) {
			return self::maybe_desc_empty_for_default();
		}

		return $default;
	}

	private static function maybe_desc_empty_for_default() {
		return _x('Leave empty for the default value','admin','listowp');
	}
}

if(is_admin()) {
	Listo_Config_Panel::get_instance();
}