<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

use HeimrichHannot\PwaBundle\Contao\FrontendModule\PushSubscriptionPopupModule;

$dca = &$GLOBALS['TL_DCA']['tl_module'];

$dca['palettes'][PushSubscriptionPopupModule::TYPE] =
    '{title_legend},name,headline,type;{config_legend},pwaPopupToggle;{content_legend},pwaText;{image_legend},addImage;{template_legend:hide},pwaPopupTemplate,pwaSubscribeButtonTemplate,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$fields = &$dca['fields'];

$fields['pwaSubscribeButtonTemplate'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
    'sql' => "varchar(64) NOT NULL default ''",
];

$fields['pwaPopupTemplate'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
    'sql' => "varchar(64) NOT NULL default ''",
];

$fields['pwaPopupToggle'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options' => [
        PushSubscriptionPopupModule::TOGGLE_EVENT,
        PushSubscriptionPopupModule::TOGGLE_CUSTOM,
    ],
    'default' => PushSubscriptionPopupModule::TOGGLE_EVENT,
    'eval' => ['tl_class' => 'w50 clr', 'helpwizard' => true],
    'explanation' => 'pwaPopupToggle',
    'sql' => "varchar(64) NOT NULL default ''",
];

$fields['pwaText'] = [
    'exclude' => true,
    'search' => false,
    'filter' => false,
    'inputType' => 'textarea',
    'eval' => ['mandatory' => false, 'rte' => 'tinyMCE', 'helpwizard' => true],
    'explanation' => 'insertTags',
    'sql' => "mediumtext NULL",
];
