<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 23.08.18
 * Time: 10:08
 */


return [

    'table_names' => [
        'board' => 'boards',
        'board_categories' => 'board_categories',
        'card' => 'cards',
        'ticket' => 'tickets',
        'user' => 'ticket_users',
        'ticket_categories' => 'ticket_categories',
        'ticket_outsource' => 'ticket_outsources',
        'outsource_reports' => 'outsource_reports',
    ],
    'max_boards' => 400,
    'max_cards' => 4000,
    'prefix' => 'your-company',
    'card_prefix' => 'your-company_cards',
    'callback' => getenv('TICKETING_CALLBACK', ''),
];
