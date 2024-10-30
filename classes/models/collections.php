<?php

class Listo_Model_Collections extends Listo_Model_List {

	public static function name($collection)
	{
		$defaults = [
			'due' => Listo_Config::get('collections_due_label',self::default_name('due'), 1),
			'scheduled' => Listo_Config::get('collections_scheduled_label', self::default_name('scheduled'),1),
			'recurring' => Listo_Config::get('collections_recurring_label', Listo_Model_Collections::default_name('recurring'),1),
			'all' => Listo_Config::get('collections_all_label',self::default_name('all'),1),
			'inbox' => Listo_Config::get('collections_inbox_label',self::default_name('inbox'),1),
			'done'  => Listo_Config::get('collections_done_label',self::default_name('done'),1),
		];



		return isset($defaults[$collection])  ? $defaults[$collection] : __('List','listowp');
	}

	public static function default_name($collection)
	{
		$defaults = [
			'due' => _x('Expired','Tasks type/state','listowp'),
			'scheduled' => _x('Scheduled','Tasks type/state', 'listowp'),
			'recurring' => _x('Recurring','Tasks type/state', 'listowp'),
			'all' => _x('To Do', 'Tasks type/state', 'listowp'),
			'inbox' => _x('Inbox','Tasks type/state', 'listowp'),
			'done'  => _x('Done','Tasks type/state', 'listowp'),
		];

		return isset($defaults[$collection])  ? $defaults[$collection] : 'fa-solid fa-list';
	}

	public static function meta_icon($collection) {
		$defaults = [
			'due' => Listo_Config::get('collections_meta_due_icon',self::default_meta_icon('due'), 1),
			'scheduled' => Listo_Config::get('collections_meta_scheduled_icon', self::default_meta_icon('scheduled'),1),
			'all' => Listo_Config::get('collections_meta_all_icon',self::default_meta_icon('all'),1),
			'done'  => Listo_Config::get('collections_meta_done_icon',self::default_meta_icon('done'),1),
		];

		return isset($defaults[$collection])  ? $defaults[$collection] : 'fa-solid fa-list-ul';
	}

	public static function default_meta_icon($collection)
	{
		$defaults = [
			'due' => 'fa-solid fa-circle-exclamation',
			'scheduled' => 'fa-solid fa-calendar-days',
			'all' => 'fa-regular fa-circle-check',
			'inbox' => 'fa-solid fa-inbox',
			'done'  => 'fa-solid fa-circle-check',
		];

		return isset($defaults[$collection])  ? $defaults[$collection] : 'fa-solid fa-list-ul';
	}

	public static function icon($collection)
	{
		$defaults = [
			'due' => Listo_Config::get('collections_due_icon',self::default_icon('due'), 1),
			'scheduled' => Listo_Config::get('collections_scheduled_icon', self::default_icon('scheduled'),1),
			'recurring' => Listo_Config::get('collections_recurring_icon', Listo_Model_Collections::default_icon('recurring'),1),
			'all' => Listo_Config::get('collections_all_icon',self::default_icon('all'),1),
			'inbox' => Listo_Config::get('collections_inbox_icon',self::default_icon('inbox'),1),
			'done'  => Listo_Config::get('collections_done_icon',self::default_icon('done'),1),
		];

		return isset($defaults[$collection])  ? $defaults[$collection] : 'fa-solid fa-list-ul';
	}

	public static function default_icon($collection)
	{
		$defaults = [
			'due' => 'fa-solid fa-exclamation',
			'scheduled' => 'fa-solid fa-calendar-days',
			'recurring' => 'fa-solid fa-repeat',
			'all' => 'fa-solid fa-list-ul',
			'inbox' => 'fa-solid fa-inbox',
			'done'  => 'fa-solid fa-check',
		];

		return isset($defaults[$collection]) ? $defaults[$collection] : 'fa-solid fa-list-ul';
	}

	public static function color($collection)
	{
		$defaults = [
			'due' => Listo_Config::get('collections_due_color',self::default_color('due'), 1),
			'scheduled' => Listo_Config::get('collections_scheduled_color', self::default_color('scheduled'),1),
			'recurring'  => Listo_Config::get('collections_done_color',self::default_color('recurring'),1),
			'all' => Listo_Config::get('collections_all_color',self::default_color('all'),1),
			'inbox' => Listo_Config::get('collections_inbox_color',self::default_color('inbox'),1),
			'done'  => Listo_Config::get('collections_done_color',self::default_color('done'),1),
		];

		return isset($defaults[$collection])  ? $defaults[$collection] : __('List','listowp');
	}

	public static function default_color($collection)
	{
		$defaults = [
			'due' => '#CB041F',
			'scheduled' => '#F4631E',
			'recurring' => '#F4631E',
			'all' => '#FF9F00',
			'inbox' => '#309898',
			'done'  => '#69B34C',
			'custom' => '#007E7E',
		];

		return isset($defaults[$collection])  ? $defaults[$collection] : __('List','listowp');
	}

    public function __construct(WP_REST_Request $request) {
        parent::__construct($request);
    }

    public function read()
    {
		$this->init('collection');
        $this->query_start();

        $this->query_where();
        $this->query_order();

	    $ids=[];

		// Attach smart collections only if we are requesting a full list
		if($this->request->get_method() == 'GET' && !isset($this->request['id'])) {
			$default_ids = [
				'due',
				'scheduled',
				'all',
				'done',
				'inbox',
				'recurring',
			];

	        foreach($default_ids as $id) {
	            if($id=='inbox' || Listo_Config::get("collections_{$id}_enabled")) {
	                $ids[]=$id;
	            }
	        }
		}

		$result = $this->wpdb->get_results($this->query, ARRAY_A);
		foreach($result as $id) {
			$ids[]=$id['id'];
		}
        return $this->prepare_result($ids);
    }

    public function reorder()
    {
        $this->init('collection');
        parent::reorder();
    }
}