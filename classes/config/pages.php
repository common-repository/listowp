<?php

class Listo_Pages {
	private static $instance;

	public static function get_instance()
	{
		return isset(self::$instance) ? self::$instance : self::$instance = new self;
	}

	private function __construct() {

		// Rebuild cache if MayFly expired
		add_action('admin_init', function() {

            // Do not fire if license warning is in place
			if(apply_filters('listowp_is_pro', FALSE) && !Listo_Mayfly::get('license_valid')) {
				return;
			}

			if(NULL === Listo_Mayfly::get('pages')) {
				$this->find_pages();
			}
		});

		add_action('admin_notices', function() {

			if(apply_filters('listowp_is_pro', FALSE)) {
                // Don't fire for Pro if license is not configured yet or any of the integrations is enabled
                if(!Listo_Mayfly::get('license_valid')) { return; }
				if(Listo_Config::get('peepso_enabled')) { return; }
				if(Listo_Config::get('bb_enabled')) { return; }
				if(Listo_Config::get('woo_enabled')) { return; }
			}

			if(!is_array(Listo_Mayfly::get('pages')) && Listo_Mayfly::get('pages_did_check')) {?>
				<div class="error listowp">
                <?php $content = [
                    'intro' => '<b>ListoWP:</b> please <a target="_blank" href="'.admin_url('/post-new.php?post_type=page').'">add a page</a> with a <b>[listowp] shortcode</b> or <b>block</b>.',
                    'pro' => '',
                    'outro' => '<br/>For more details, please refer to the <a href="https://listowp.com/r/docs-getting-started" target="_blank">getting started guide</a>.</div>',
                ];

                $content =  apply_filters('listowp_getting_started',$content);

                echo implode(' ',$content);
			}
		});

		// Rebuild cache when any post is saved
		add_action('publish_post', [$this, 'find_pages']);
		add_action('save_post', [$this, 'find_pages']);
		add_action('untrashed_post', [$this, 'find_pages']);
		add_action('trashed_post', [$this, 'find_pages']);
		add_action('transition_post_status', [$this, 'find_pages']);
	}

	public function find_pages() {
		if(!Listo_Mayfly::get('license_valid')) {
			return;
		}
		Listo_Mayfly::del('pages');
		Listo_Mayfly::del('pages_did_check');
		global $wpdb;
		$sql="SELECT id FROM $wpdb->posts WHERE post_type='page' AND post_status='publish' AND (post_content LIKE '%[listowp]%' OR post_content LIKE '%wp:listowp/block%')";
		$rows = $wpdb->get_results($sql, ARRAY_A);
		$result=[];
		if(count($rows)) {
			foreach ( $rows as $page ) {
				$result[]=$page['id'];
			}
		}

		if(!count($result)) { $result =0; }

		Listo_Mayfly::set('pages', $result, 3600);
		Listo_Mayfly::set('pages_did_check', time(), -1);
	}
}


Listo_Pages::get_instance();
