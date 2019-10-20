<?php
/**
 * contao-intranet-bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 47GradNord - Agentur für Internetlösungen
 *
 * @license    commercial
 * @author     Holger Neuner
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['vrp_menueslist'] = '
{title_legend},name,type;
{notification_legend},urban_includeModule;
{protected_legend:hide},protected;
{template_legend:hide},customTpl;
{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['vrp_selectNotification'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['vrp_selectNotification'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => ['Vrpayment\ContaoIntranetBundle\Backend', 'getNotifications'],
    'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default ''",
];
