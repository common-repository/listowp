<?php

abstract class Listo_REST_V1_Endpoint {

    protected static $instance;

    protected $wpdb;
    protected $request;

    protected $model;
    public $items=[];

    protected $links = NULL;

    public $state;

    public static $cred = array(
        'create'    => WP_REST_Server::CREATABLE,
        'read'      => WP_REST_Server::READABLE,
        'edit'      => WP_REST_Server::EDITABLE,
        'delete'    => WP_REST_Server::DELETABLE,
    );

    public function __construct()
    {
        $this->wpdb = $GLOBALS['wpdb'];
    }

    final public static function get_instance() {

        static $instances = array();

        $called_class = get_called_class();

        if (!isset($instances[$called_class]))
        {
            $instances[$called_class] = new $called_class();
        }

        return $instances[$called_class];
    }

    public function can($method) {

		// By default, any logged-in user can perform API actions.
	    // Control over collection/item IDs is implemented in the endpoints
        if(Listo_User::get_id()) {
            return TRUE;
        }
    }

    protected function rest_route_for_class($endpoint, $id=NULL) {
        $url = Listo_API::REST_NAMESPACE . '/' . Listo_API::REST_V.'/'.$endpoint;
        if($id) {
            $url .= '/'.$id;
        }
        return rest_url($url);
    }

    protected function items_to_response() {

        if(!is_array($this->items) || !count($this->items)) {
            return rest_ensure_response([]);
        }
        // Single item
        if ($this->request['id']) {
            $this->items=[$this->items];
        }

        foreach ($this->items as $item) {

            $response = rest_ensure_response($item);

            if(is_array($this->links) && count($this->links)) {

                $links = [];
                foreach($this->links as $key=>$config) {
                    $links[$key] = [
                        'href' => $this->rest_route_for_class($config['endpoint'], $item[$config['object_key']]),
                        'embeddable' => $config['embeddable'],
                        ];
                }

                $response->add_links($links);
            }

            $server = new WP_REST_Server();
            $response = $server->response_to_data($response, $this->request['_embed']);

            $result[] = $response;
        }

        // Single item
        if ($this->request['id']) {
            $result = $response;
        }

        return rest_ensure_response($result);

    }

    protected function configure_link($key=NULL, $endpoint=NULL, $object_key=NULL, $embeddable=NULL,$fields=NULL) {
//        if(NULL===$key) {
//            $key = 'self';
//            $endpoint = explode('_', get_class($this));
//            $endpoint = strtolower($endpoint[count($endpoint)-1]);
//            $object_key ='id';
//            $embeddable = TRUE;
//        }
        $this->links[$key] = ['endpoint'=>$endpoint,'object_key'=>$object_key,'embeddable'=>$embeddable];
    }

    public function init(WP_REST_Request $request) {
        $this->request = $request;
    }
}