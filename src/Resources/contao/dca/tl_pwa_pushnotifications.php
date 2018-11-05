<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$table = 'tl_pwa_pushnotifications';

$GLOBALS['TL_DCA'][$table] = [
	'config'   => [
		'dataContainer'    => 'Table',
		'ptable'           => 'tl_pwa_configurations',
		'enableVersioning' => true,
		'sql'              => [
			'keys' => [
				'id' => 'primary',
			],
		],
	],
	'list'     => [
		'label'             => [
			'fields' => ['title'],
			'format' => '%s',
		],
		'sorting'           => [
			'mode'         => 1,
			'fields'       => ['title'],
			'headerFields' => ['title'],
			'panelLayout'  => 'filter;search,limit',
		],
		'global_operations' => [
			'all' => [
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();"',
			],
		],
		'operations'        => [
			'edit'   => [
				'label' => &$GLOBALS['TL_LANG']['MSC']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			],
			'copy'   => [
				'label' => &$GLOBALS['TL_LANG']['MSC']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif',
			],
			'delete' => [
				'label'      => &$GLOBALS['TL_LANG']['MSC']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
					. '\'))return false;Backend.getScrollOffset()"',
			],
			'show'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_pushsubscriber']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			],
		],
	],
	'palettes' => [
		'default' => '{general_legend},type;',
	],
	'fields'   => [
		'id'        => [
			'sql' => "int(10) unsigned NOT NULL auto_increment",
		],
		'pid'                    => [
			'foreignKey' => 'tl_pwa_configurations.title',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => ['type' => 'belongsTo', 'load' => 'eager'],
		],
		'tstamp'    => [
			'label' => &$GLOBALS['TL_LANG']['tl_cleaner']['tstamp'],
			'sql'   => "int(10) unsigned NOT NULL default '0'",
		],
		'dateAdded' => [
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'flag'    => 6,
			'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		],
		'title'                 => [
			'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['title'],
			'inputType'  => 'text',
			'eval'       => ['tl_class'  => 'w50'],
			'sql'        => "varchar(255) NOT NULL default ''",
		],
		'body'                 => [
			'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['body'],
			'inputType'  => 'text',
			'eval'       => ['tl_class'  => 'w50'],
			'sql'        => "varchar(128) NOT NULL default ''",
		],
		'icon'                 => [
			'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['icon'],
			'inputType' => 'fileTree',
			'eval'      => [
				'files'      => true,
				'filesOnly'  => true,
				'extensions' => 'jpg,png,gif',
				'fieldType'  => 'radio',
				'tl_class'   => 'clr',
			],
			'sql'       => "blob NULL",
		],
		'sendDate' => [
			'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['sendDate'],
			'inputType'  => 'text',
			'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		],
		'sent' => [
			'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['sent'],
			'inputType'  => 'checkbox',
			'eval'    => [],
			'sql'     => "char(1) NOT NULL default ''",
		],
	],
];