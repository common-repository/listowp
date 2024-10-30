<?php

add_filter('listowp_frontend_data', function($data) {
    $assets = Listo_Frontend_Assets::get_instance();

    $pro_data = [
        'templates' => [
            'recurring' => $assets->get_template('pro/item_due_recurring'),
        ]
    ];

    return array_merge_recursive($data, $pro_data);
});
