<?php

$GLOBALS['TL_DCA']['tl_pwa_subscriber'] = [
	'config'   => [
		'dataContainer'     => 'Table',
		'enableVersioning'  => true,
		'onsubmit_callback' => [
			['huh.utils.dca', 'setDateAdded'],
		],
		'sql'               => [
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
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_subscriber']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			],
			'copy'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_subscriber']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif',
			],
			'delete' => [
				'label'      => &$GLOBALS['TL_LANG']['tl_pwa_subscriber']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
					. '\'))return false;Backend.getScrollOffset()"',
			],
			'show'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_subscriber']['show'],
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
	],
];