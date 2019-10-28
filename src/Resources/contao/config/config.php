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
$GLOBALS['FE_MOD']['vrpayment-intranet']['vrp_menuescronjob'] = 'Vrpayment\ContaoIntranetBundle\Module\MenuesCronjob';

/**
 * Register Models
 */
$GLOBALS['TL_MODELS']['tl_vrp_intranet_menue'] = 'Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueModel';
$GLOBALS['TL_MODELS']['tl_vrp_intranet_menue_cart'] = 'Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueCartModel';

/*
 * Notification Center
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['vrp_intranet'] = [
    'intranet_notification' => [
        'email_sender_address' => ['admin_mail'],
        'email_sender_name' => ['admin_name'],
        'recipients' => ['member_email', 'admin_email'],
        'email_subject' => ['member_*', 'orderdate'],
        'email_text' => ['member_*', 'member_name', 'orderdate', 'order'],
    ],
    'intranet_admin_notification' => [
        'email_sender_address' => ['admin_mail'],
        'email_sender_name' => ['admin_name'],
        'recipients' => ['member_email', 'admin_mail'],
        'email_subject' => ['member_*', 'orderdate'],
        'email_text' => ['member_*', 'member_name', 'orderdate', 'orders'],
        'attachment_tokens'    => [
            'exportfile'
        ]
    ],
];

