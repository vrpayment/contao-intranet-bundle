<?php
/**
 * contao-intranet-bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 47GradNord - Agentur für Internetlösungen
 *
 * @license    commercial
 * @author     Holger Neuner
 */

/**
 * Backend Modules
 */
array_insert($GLOBALS['BE_MOD']['content'], count($GLOBALS['BE_MOD']['content']), ['vrpayment_intranet_menue' => [
    'tables' => ['tl_vrp_intranet_menue'], ],
]);

array_insert($GLOBALS['BE_MOD'], 0, [
    'vrpayment-intranet' => [
        'menue' => [
            'tables' => ['tl_vrp_intranet_menue'],
        ],
    ],
]);
