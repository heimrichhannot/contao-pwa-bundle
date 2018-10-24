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

$dca['config']['oncreate_version_callback'][] = ['huh.pwa.datacontainer.page', 'oncreateVersionCallback'];

$dca['palettes']['__selector__'][] = 'addPwa';
$dca['palettes']['root']           = str_replace('{publish_legend', '{pwa_legend},addPwa;{publish_legend', $dca['palettes']['root']);
$dca['subpalettes']['addPwa']      = 'pwaName,pwaShortName,pwaDescription,pwaThemeColor,pwaBackgroundColor,pwaIcons,pwaDirection,pwaDisplay,pwaOrientation,pwaStartUrl,pwaScope,pwaRelatedApplications,pwaPreferRelatedApplication';

$fields = [
	'addPwa'                      => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['addPwa'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
		'sql'       => "char(1) NOT NULL default ''"
	],
	'pwaName'                     => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaName'],
		'inputType' => 'text',
		'eval'      => [
			'maxlength' => 128,
			'tl_class'  => 'w50 clr',
		],
		'sql'       => "varchar(128) NOT NULL default ''",
	],
	'pwaShortName'                => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaShortName'],
		'inputType' => 'text',
		'eval'      => [
			'maxlength' => 32,
			'tl_class'  => 'w50',
		],
		'sql'       => "varchar(32) NOT NULL default ''",
	],
	'pwaBackgroundColor'          => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaBackgroundColor'],
		'inputType' => 'text',
		'eval'      => ['maxlength' => 6, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'],
		'sql'       => "varchar(16) NOT NULL default ''",
	],
	'pwaThemeColor'               => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaThemeColor'],
		'inputType' => 'text',
		'eval'      => ['maxlength' => 6, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'],
		'sql'       => "varchar(64) NOT NULL default ''",
	],
	'pwaDescription'              => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['manifestDescription'],
		'inputType' => 'textarea',
		'eval'      => [
			'tl_class' => 'clr',
		],
		'sql'       => "text NULL",
	],
	'pwaDirection'                => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaDirection'],
		'inputType' => 'select',
		'options'   => \HeimrichHannot\ContaoPwaBundle\Manifest\Manifest::DIR_VALUES,
		'reference' => &$GLOBALS['TL_LANG']['tl_page']['pwaDir'],
		'eval'      => [
			'maxlength' => 4,
			'tl_class'  => 'w50',
			'includeBlankOption' => true,
		],
		'sql'       => "varchar(4) NOT NULL default ''",
	],
	'pwaDisplay'                  => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaDisplay'],
		'inputType' => 'select',
		'options'   => \HeimrichHannot\ContaoPwaBundle\Manifest\Manifest::DISPLAY_VALUES,
		'reference' => &$GLOBALS['TL_LANG']['tl_page']['pwaDisplay'],
		'eval'      => ['tl_class' => 'w50','includeBlankOption' => true,],
		'sql'       => "varchar(16) NOT NULL default ''",
	],
	'pwaIcons'                    => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaIcons'],
		'inputType' => 'fileTree',
		'eval'      => [
			'files'      => true,
			'filesOnly'  => true,
			'extensions' => 'jpg,png,gif,svg',
			'fieldType'  => 'radio',
			'tl_class'   => 'clr',
		],
		'sql'       => "blob NULL",

	],
	'pwaOrientation'              => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaOrientation'],
		'inputType' => 'select',
		'options'   => \HeimrichHannot\ContaoPwaBundle\Manifest\Manifest::ORIENTATION_VALUES,
		'reference' => &$GLOBALS['TL_LANG']['tl_page']['pwaOrientation'],
		'eval'      => ['tl_class' => 'w50 clr','includeBlankOption' => true,],
		'sql'       => "varchar(32) NOT NULL default ''",
	],
	'pwaStartUrl'                 => [
		'label'      => &$GLOBALS['TL_LANG']['tl_page']['pwaStartUrl'],
		'inputType'  => 'pageTree',
		'foreignKey' => 'tl_page.title',
		'relation'   => [
			'type' => 'hasOne',
			'load' => 'lazy'
		],
		'eval'       => [
			'fieldType' => 'radio',
			'tl_class'  => 'w50 clr',
		],
		'sql'        => "varchar(255) NOT NULL default ''",
	],
	'pwaScope'                    => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaScope'],
		'inputType' => 'text',
		'eval'      => [
			'maxlength' => 256,
			'tl_class'  => 'w50',
		],
		'sql'       => "varchar(256) NOT NULL default ''",
	],
	'pwaPreferRelatedApplication' => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaPreferRelatedApplication'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => ['tl_class' => 'w50'],
		'sql'       => "char(1) NOT NULL default ''"
	],
	'pwaRelatedApplications'      => [
		'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaRelatedApplications'],
		'exclude'   => true,
		'inputType' => 'multiColumnWizard',
		'eval'      => [
			'tl_class' => 'clr',
			'columnFields' => [
				'plattform' => [
					'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaRelatedApplications_plattform'],
					'exclude'   => true,
					'inputType' => 'text',
					'eval'      => ['style' => 'width:180px']
				],
				'url'       => [
					'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaRelatedApplications_url'],
					'exclude'   => true,
					'inputType' => 'text',
					'eval'      => ['style' => 'width:180px']
				],
				'id'        => [
					'label'     => &$GLOBALS['TL_LANG']['tl_page']['pwaRelatedApplications_id'],
					'exclude'   => true,
					'inputType' => 'text',
					'eval'      => ['style' => 'width:180px']
				],
			],
		],
		'sql'       => 'blob NULL',
	],
];

$dca['fields'] += $fields;