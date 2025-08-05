<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;

$dca = &$GLOBALS['TL_DCA']['tl_page'];

$pm = PaletteManipulator::create()
    ->addLegend('pwa_legend', 'publish_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField('addPwa', 'pwa_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page');

if (isset($dca['palettes']['rootfallback'])) {
    $pm->applyToPalette('rootfallback', 'tl_page');
}

$dca['palettes']['__selector__'][] = 'addPwa';
$dca['subpalettes']['addPwa_yes'] = 'pwaConfiguration';
$dca['subpalettes']['addPwa_inherit'] = 'pwaParent';

$fields = &$dca['fields'];

$fields['addPwa'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options' => [PageContainer::ADD_PWA_NO, PageContainer::ADD_PWA_YES],
    'reference' => &$GLOBALS['TL_LANG']['tl_page']['addPwa'],
    'eval' => [
        'tl_class' => 'w50 clr',
        'submitOnChange' => true,
        'default' => PageContainer::ADD_PWA_NO,
        "includeBlankOption" => false,
    ],
    'sql' => "varchar(10) NOT NULL default ''",
];

$fields['pwaConfiguration'] = [
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true,],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];

$fields['pwaParent'] = [
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true,],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];

$dca['fields'] += $fields;
