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
			'fields' => ['title','sendDate', 'sent'],
			'format' => '%s',
			'label_callback' => ['huh.pwa.datacontainer.pwapushnotification','onLabelCallback'],
		],
		'sorting'           => [
			'mode'         => 4,
			'fields'       => ['sendDate DESC'],
			'panelLayout'  => 'filter;sort,search,limit',
			'flat' => 6,

			'headerFields'            => ['title'],
			'child_record_callback'   => ['huh.pwa.datacontainer.pwapushnotification','onLabelCallback'],
//			'child_record_class'      => 'no_padding'
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
		'default' => '{message_legend},title,body,icon;{send_legend},sendDate;',
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
			'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['sendDate'],
			'inputType' => 'text',
			'default'   => time(),
			'eval'      => ['rgxp' => 'datim', 'doNotCopy' => true, 'tl_class' => 'w50', 'datepicker' => true],
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'flag'      => 8,
		],
		'sent'     => [
			'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['sent'],
			'inputType' => 'checkbox',
			'filter'    => true,
			'eval'      => [],
			'sql'       => "char(1) NOT NULL default ''",

		],
		'receiverCount' => [
			'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['sent'],
			'inputType'  => 'checkbox',
			'eval'    => [],
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		],
	],
];