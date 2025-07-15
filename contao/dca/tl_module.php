<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

use HeimrichHannot\PwaBundle\DataContainer\ModuleContainer;
use HeimrichHannot\PwaBundle\FrontendModule\PushSubscriptionPopupFrontendModule;

$dca = &$GLOBALS['TL_DCA']['tl_module'];

$dca['config']['onload_callback']['huh_pwa'] = [ModuleContainer::class, 'onLoadCallback'];

$dca['palettes'][PushSubscriptionPopupFrontendModule::TYPE] = '{title_legend},name,headline,type;{config_legend},pwaPopupToggle;{content_legend},pwaText;{image_legend},addImage;{template_legend:hide},pwaPopupTemplate,pwaSubscribeButtonTemplate,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['fields']['pwaSubscribeButtonTemplate'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['pwaSubscribeButtonTemplate'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => [ModuleContainer::class, 'onPwaSubscribeButtonTemplateOptionsCallback'],
    'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
    'sql'              => "varchar(64) NOT NULL default ''",
];
$dc['fields']['pwaPopupTemplate'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['pwaPopupTemplate'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => [ModuleContainer::class, 'onPwaPopupTemplateOptionsCallback'],
    'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
    'sql'              => "varchar(64) NOT NULL default ''",
];
$dc['fields']['pwaPopupToggle'] = [
    'label'       => &$GLOBALS['TL_LANG']['tl_module']['pwaPopupToggle'],
    'exclude'     => true,
    'inputType'   => 'select',
    'options'     => [
        PushSubscriptionPopupFrontendModule::TOGGLE_EVENT,
        PushSubscriptionPopupFrontendModule::TOGGLE_CUSTOM,
    ],
    'default' => PushSubscriptionPopupFrontendModule::TOGGLE_EVENT,
    'eval'        => ['tl_class' => 'w50 clr', 'helpwizard' => true],
    'explanation' => 'pwaPopupToggle',
    'sql'         => "varchar(64) NOT NULL default ''",
];
$dc['fields']['pwaText'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['text'],
    'exclude'                 => true,
    'search'                  => false,
    'filter'                  => false,
    'inputType'               => 'textarea',
    'eval'                    => array('mandatory'=>false, 'rte'=>'tinyMCE', 'helpwizard'=>true),
    'explanation'             => 'insertTags',
    'sql'                     => "mediumtext NULL"
];