<?php

$GLOBALS['TL_DCA']['tl_pwa_pushsubscriber'] = [
	'config'   => [
		'dataContainer'     => 'Table',
		'ptable'           => 'tl_pwa_configurations',
		'enableVersioning'  => false,
		'closed' => true,
		'sql'               => [
			'keys' => [
				'id' => 'primary',
			],
		],
	],
	'list'     => [
		'label'             => [
			'fields' => ['endpoint'],
			'format' => '%s',
		],
		'sorting'           => [
			'mode'         => 2,
			'fields'       => ['dateAdded'],
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
		'endpoint'                 => [
			'label'      => &$GLOBALS['TL_LANG']['tl_page']['endpoint'],
			'inputType'  => 'text',
			'eval'       => ['tl_class'  => 'w50'],
			'sql'        => "varchar(255) NOT NULL default ''",
		],
		'publicKey'                 => [
			'label'      => &$GLOBALS['TL_LANG']['tl_page']['authToken'],
			'inputType'  => 'text',
			'eval'       => ['tl_class'  => 'w50'],
			'sql'        => "varchar(128) NOT NULL default ''",
		],
		'authToken'                 => [
			'label'      => &$GLOBALS['TL_LANG']['tl_page']['authToken'],
			'inputType'  => 'text',
			'eval'       => ['tl_class'  => 'w50'],
			'sql'        => "varchar(128) NOT NULL default ''",
		],
	],
];