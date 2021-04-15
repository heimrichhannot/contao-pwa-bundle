<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dca = &$GLOBALS['TL_DCA']['tl_page'];

$dca['config']['oncreate_version_callback'][] = [\HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::class, 'onCreateVersionCallback'];

$dca['palettes']['__selector__'][] = 'addPwa';
$dca['palettes']['root']           = str_replace('{publish_legend', '{pwa_legend},addPwa;{publish_legend', $dca['palettes']['root']);
if (isset($dca['palettes']['rootfallback'])) {
    $dca['palettes']['rootfallback']           = str_replace('{publish_legend', '{pwa_legend},addPwa;{publish_legend', $dca['palettes']['rootfallback']);
}
$dca['subpalettes']['addPwa_yes']      = 'pwaConfiguration';
$dca['subpalettes']['addPwa_inherit']      = 'pwaParent';

$fields = [
	'addPwa'           => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['addPwa'],
		'exclude'   => true,
		'inputType' => 'select',
		'options'   => [
			\HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::ADD_PWA_NO,
			\HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::ADD_PWA_YES
		],
		"reference" => &$GLOBALS['TL_LANG']['tl_page']['addPwa'],
		'eval'      => [
			'tl_class' => 'w50 clr',
			'submitOnChange' => true,
			"default" => \HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::ADD_PWA_NO,
			"includeBlankOption" => false,
		],
		'sql'       => "varchar(10) NOT NULL default ''"
	],
	'pwaConfiguration' => [
		'label'            => &$GLOBALS['TL_LANG']['tl_page']['pwaConfiguration'],
		'inputType'        => 'select',
		'options_callback' => [\HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::class, 'getPwaConfigurationsAsOptions'],
		'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true,],
		'sql'              => "int(10) unsigned NOT NULL default '0'",
	],
	'pwaParent'        => [
		'label'            => &$GLOBALS['TL_LANG']['tl_page']['pwaParent'],
		'inputType'        => 'select',
		'options_callback' => [\HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::class, 'getInheritPwaPageConfigOptions'],
		'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true,],
		'sql'              => "int(10) unsigned NOT NULL default '0'",
	],
];

$dca['fields'] += $fields;