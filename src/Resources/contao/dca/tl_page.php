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

$dca['config']['oncreate_version_callback'][] = ['huh.pwa.datacontainer.page', 'onCreateVersionCallback'];

$dca['palettes']['__selector__'][] = 'addPwa';
$dca['palettes']['root']           = str_replace('{publish_legend', '{pwa_legend},addPwa;{publish_legend', $dca['palettes']['root']);
$dca['subpalettes']['addPwa']      = 'pwaConfiguration';

$fields = [
	'addPwa'                      => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['addPwa'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
		'sql'       => "char(1) NOT NULL default ''"
	],
	'pwaConfiguration'              => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaConfiguration'],
		'inputType' => 'select',
		'options_callback'   => ['huh.pwa.datacontainer.page', 'getPwaConfigurationsAsOptions'],
		'eval'      => ['tl_class' => 'w50 clr','includeBlankOption' => true,],
		'sql'       => "int(10) unsigned NOT NULL default '0'",
	],
];

$dca['fields'] += $fields;