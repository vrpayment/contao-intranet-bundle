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

array_insert($GLOBALS['BE_MOD'], 0, [
    'vrpayment-intranet' => [
        'menue' => [
            'tables' => ['tl_vrp_intranet_menue'],
        ],
    ],
]);

/**
 * Frontend Modules
 */
$GLOBALS['FE_MOD']['vrpayment-intranet']['vrp_menueslist'] = 'Vrpayment\ContaoIntranetBundle\Module\MenuesList';

/**
 * Register Models
 */
$GLOBALS['TL_MODELS']['tl_vrp_intranet_menue'] = 'Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueModel';

