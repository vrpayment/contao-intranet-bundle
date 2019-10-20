<?php

$GLOBALS['TL_DCA']['tl_vrp_intranet_menue_cart'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'member' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'type' => [
            'sql' => "varchar(16) NOT NULL default ''",
        ],
        'items' => [
            'sql'                     => "blob NULL"
        ],
        'completed' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'token' => [
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
