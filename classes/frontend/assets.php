<?php

class Listo_Frontend_Assets
{
    private static $instance;

    public $assets_url;
    public $assets_dir;
    public $templates_dir;
    public static function get_instance()
    {
        return isset(self::$instance) ? self::$instance : self::$instance = new self;
    }

    private function __construct()
    {
        $this->assets_url = plugin_dir_url(dirname(dirname(__FILE__))) . 'assets/';
        $this->assets_dir = plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/';
        $this->templates_dir = plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/';

        add_action('init', [$this, 'register_block_type']);
        add_action('init', [$this, 'register_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'register_frontend_assets']);
    }

    public function is_dev()
    {
        return defined('LISTOWP_DEV') && LISTOWP_DEV;
    }

    public function register_block_type()
    {
        if (!function_exists('register_block_type')) {
            return;
        }

        $this->register_frontend_assets();

        wp_register_script('listowp-block-editor', "{$this->assets_url}js/block-editor.js", ['wp-blocks', 'wp-i18n', 'wp-element'], Listo_Plugin::PLUGIN_VERSION, true);
        wp_localize_script('listowp-block-editor', 'listoBlockEditorData', []);

        register_block_type('listowp/block', [
            'style'=> 'listowp',
            'script'=> 'listowp',
            'editor_script' => 'listowp-block-editor',
            'render_callback' => [$this, 'render_component'],
        ]);
    }

    public function register_shortcode()
    {
        add_shortcode('listowp', function() {
            ob_start();
            echo $this->render_component();
            return ob_get_clean();
        });
    }

    public function render_component()
    {
        wp_enqueue_style('listowp');
        wp_enqueue_script('listowp');
        if (apply_filters('listowp_is_pro', FALSE)) {
            wp_enqueue_script('listowp-pro');
        }

        return '<div class="listo" data-listo="root"></div>';
    }

    public function register_frontend_assets()
    {
        // Do not re-register if scripts are already registered.
        if (wp_script_is('listowp', 'registered')) {
            return;
        }

        wp_register_style('listowp-font', "https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap", [], Listo_Plugin::PLUGIN_VERSION, 'all');
        wp_register_style('listowp-fontawesome', $this->get_assets_url("css/icons.css"), [], Listo_Plugin::PLUGIN_VERSION, 'all');
        wp_register_style('listowp', $this->get_assets_url("css/frontend.css"), ['listowp-fontawesome', 'listowp-font'], Listo_Plugin::PLUGIN_VERSION, 'all');

        if ($this->is_dev()) {
            $order = json_decode(file_get_contents("{$this->assets_dir}js/frontend/index.json"));
            $handle = null;
            foreach ($order as $file) {
                $dependencies = array_merge(['jquery', 'underscore', 'wp-api-request'], $handle ? [$handle] : []);
                $handle = 'listowp' . ('init' === $file ? '' : "-{$file}");
                wp_register_script($handle, $this->get_assets_url("js/frontend/{$file}.js"), $dependencies, Listo_Plugin::PLUGIN_VERSION, true);
            }

            if (apply_filters('listowp_is_pro', FALSE)) {
                $order = json_decode(file_get_contents("{$this->assets_dir}js/frontend-pro/index.json"));
                $handle = null;
                foreach ($order as $file) {
                    $dependencies = array_merge(['listowp'], $handle ? [$handle] : []);
                    $handle = 'listowp-pro' . ('init' === $file ? '' : "-{$file}");
                    wp_register_script($handle, $this->get_assets_url("js/frontend-pro/{$file}.js"), $dependencies, Listo_Plugin::PLUGIN_VERSION, true);
                }
            }
        } else {
            wp_register_script('listowp', "{$this->assets_url}js/frontend.js",
                ['jquery', 'underscore', 'wp-api-request'], Listo_Plugin::PLUGIN_VERSION, true);
            if (apply_filters('listowp_is_pro', FALSE)) {
                wp_register_script('listowp-pro', "{$this->assets_url}js/frontend-pro.js", ['listowp'], Listo_Plugin::PLUGIN_VERSION, true);
            }
        }

        $listoData = [
            'logged_in' => Listo_User::get_id(),
            'enable_collections' => Listo_Config::get('collections_enabled'),
            'Listo_User_get_id' => Listo_User::get_id(),
            'last_viewed_collection' => strlen($meta = (Listo_User_Preferences::get_instance())->get('last_collection')) ? $meta : 'inbox',
            'animation_speed' => 500, // in milliseconds
            'templates' => [
                'root' => $this->get_template('root'),
                'collection' => $this->get_template('collection'),
                'collection_header' => $this->get_template('collection_header'),
                'new_collection' => $this->get_template('new_collection'),
                'item' => $this->get_template('item'),
                'item_due' => $this->get_template('item_due'),
                'item_list_loading' => $this->get_template('item_list_loading'),
                'new_item' => $this->get_template('new_item'),
                'popup' => $this->get_template('popup'),
                'popup_actions' => $this->get_template('popup_actions'),
            ],
            'lang' => [
                'ok' => __('OK', 'listowp'),
                'submit' => __('Submit', 'listowp'),
                'cancel' => __('Cancel', 'listowp'),
                'popup_title' => __('Popup', 'listowp'),
//                '' => __('Deleting a list will also delete all tasks inside it. Are you sure want to proceed?', 'listowp')
                'gdpr_delete_confirmation' => __('This action will completely delete all your lists and tasks! Are you sure want to proceed?', 'listowp')
            ]
        ];

        wp_localize_script($this->is_dev() ? 'listowp-base' : 'listowp', 'listoData',
            apply_filters('listowp_frontend_data', $listoData));
    }

    public function get_assets_url($path)
    {
        $url = $this->assets_url . $path;

        if ($this->is_dev()) {
            $mtime = filemtime($this->assets_dir . $path);
            $url = add_query_arg('mtime', $mtime, $url);
        }

        return $url;
    }

    /**
     * Get template string.
     *
     * @param string $name Template name.
     * @param array  $data Optional data to be passed to the template.
     * @param array  $opts Optional settings to be applied to the template.
     * @return string
     */
    public function get_template($name, $data = [], $opts = [])
    {
        $file = $this->templates_dir . $name . '.php';
        $template = '';

        if (file_exists($file)) {
            try {
                ob_start();
                include $file;
                $template = ob_get_clean();

                if (isset($opts['trim']) && $opts['trim']) {
                    $template = preg_replace('/\s+/', ' ', $template);
                    $template = preg_replace('/> </', '><', $template);
                }
            } catch (Exception $e) {
                // Do nothing.
            }
        } else {
            new Listo_Debug("Missing template $file");
        }

        return trim($template);
    }
}
