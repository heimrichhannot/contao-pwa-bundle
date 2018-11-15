<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$table = 'tl_pwa_configurations';

$GLOBALS['TL_DCA'][$table] = [
	'config'   => [
		'dataContainer'     => 'Table',
		'enableVersioning'  => true,
		'ctable'            => 'tl_pwa_pushsubscriber',
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
			'control' => [
				'label'           => &$GLOBALS['TL_LANG']['tl_pwa_configurations']['control'],
				'href'            => 'huh_pwa_backend_control',
				'attributes'      => 'onclick="Backend.getScrollOffset();"',
				'class'           => 'header_icon',
				'icon'            => 'wrench.svg',
				'button_callback' => ['huh.pwa.datacontainer.pwaconfigurations', 'onControlActionButtonCallback']
			],
		],
		'operations'        => [
			'edit' => [
				'label'           => &$GLOBALS['TL_LANG']['tl_pwa_configurations']['edit'],
				'href'            => 'act=edit',
				'icon'            => 'header.svg',
			],
			'copy'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_configurations']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif',
			],
			'delete' => [
				'label'      => &$GLOBALS['TL_LANG']['tl_pwa_configurations']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_pwa_configurations']['deleteConfirm']
					. '\'))return false;Backend.getScrollOffset()"',
			],
			'show'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_configurations']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			],
			'subscriber'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_configurations']['subscriber'],
				'href'  => 'table=tl_pwa_pushsubscriber',
				'icon'  => 'mgroup.svg',
			],
			'pushNotifications'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_configurations']['pushNotifications'],
				'href'  => 'table=tl_pwa_pushnotifications',
				'icon'  => 'news.svg',
			],
		],
	],
	'palettes' => [
		'__selector__' => ['pwaName','sendWithCron'],
		'default' => '{general_legend},title,sendWithCron,addDebugLog;'
			        .'{manifest_legend},pwaName,pwaShortName,pwaDescription,pwaThemeColor,pwaBackgroundColor,pwaIcons,pwaDirection,pwaDisplay,pwaOrientation,pwaStartUrl,pwaScope,pwaRelatedApplications,pwaPreferRelatedApplication',
	],
	'subpalettes' => [
		'pwaName_custom' => 'pwaCustomName',
		'sendWithCron' => 'cronIntervall'
	],
	'fields'   => [
		'id'        => [
			'sql' => "int(10) unsigned NOT NULL auto_increment",
		],
		'tstamp'    => [
			'label' => &$GLOBALS['TL_LANG']['MSC']['tstamp'],
			'sql'   => "int(10) unsigned NOT NULL default '0'",
		],
		'dateAdded' => [
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'flag'    => 6,
			'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		],
		'title' => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['title'],
			'exclude'   => true,
			'search'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => ['mandatory' => true, 'maxlength' => 255],
			'sql'       => "varchar(255) NOT NULL default ''",
		],
		'addDebugLog'          => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['addDebugLog'],
			'inputType' => 'checkbox',
			'filter'    => true,
			'eval'      => ['tl_class'  => 'w50 clr'],
			'sql'       => "char(1) NOT NULL default ''",
		],
		'sendWithCron' => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['sendWithCron'],
			'inputType' => 'checkbox',
			'filter'    => true,
			'eval'      => ['tl_class'  => 'w50 clr', 'submitOnChange' => true],
			'sql'       => "char(1) NOT NULL default ''",
		],
		'cronIntervall'=> [
			'label'     => &$GLOBALS['TL_LANG'][$table]['cronIntervall'],
			'inputType' => 'select',
			'options'   => ['minutely','hourly','daily','weekly','monthly'],
			'eval'      => [
				'tl_class'  => 'w50',
				'includeBlankOption' => false,
				'default' => 'hourly'
			],
			'sql'       => "varchar(10) NOT NULL default ''",
		],
		'pwaName' => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaName'],
			'inputType' => 'select',
			'options'   => [\HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel::PWA_NAME_OPTIONS],
			'reference' => &$GLOBALS['TL_LANG'][$table]['pwaName'],
			'eval'      => [
				'tl_class'  => 'w50',
				'includeBlankOption' => false,
				'default' => 'title',
				'submitOnChange' => true
			],
			'sql'       => "varchar(10) NOT NULL default ''",
		],
		'pwaCustomName'                     => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaName'],
			'inputType' => 'text',
			'eval'      => [
				'maxlength' => 128,
				'tl_class'  => 'w50 clr',
			],
			'sql'       => "varchar(128) NOT NULL default ''",
		],
		'pwaShortName'                => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaShortName'],
			'inputType' => 'text',
			'eval'      => [
				'maxlength' => 32,
				'tl_class'  => 'w50',
			],
			'sql'       => "varchar(32) NOT NULL default ''",
		],
		'pwaBackgroundColor'          => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaBackgroundColor'],
			'inputType' => 'text',
			'eval'      => ['maxlength' => 6, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'],
			'sql'       => "varchar(16) NOT NULL default ''",
		],
		'pwaThemeColor'               => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaThemeColor'],
			'inputType' => 'text',
			'eval'      => ['maxlength' => 6, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'],
			'sql'       => "varchar(64) NOT NULL default ''",
		],
		'pwaDescription'              => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['manifestDescription'],
			'inputType' => 'textarea',
			'eval'      => [
				'tl_class' => 'clr',
			],
			'sql'       => "text NULL",
		],
		'pwaDirection'                => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaDirection'],
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
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaDisplay'],
			'inputType' => 'select',
			'options'   => \HeimrichHannot\ContaoPwaBundle\Manifest\Manifest::DISPLAY_VALUES,
			'reference' => &$GLOBALS['TL_LANG']['tl_page']['pwaDisplay'],
			'eval'      => ['tl_class' => 'w50','includeBlankOption' => true,],
			'sql'       => "varchar(16) NOT NULL default ''",
		],
		'pwaIcons'                    => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaIcons'],
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
		'pwaOrientation'              => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaOrientation'],
			'inputType' => 'select',
			'options'   => \HeimrichHannot\ContaoPwaBundle\Manifest\Manifest::ORIENTATION_VALUES,
			'reference' => &$GLOBALS['TL_LANG']['tl_page']['pwaOrientation'],
			'eval'      => ['tl_class' => 'w50 clr','includeBlankOption' => true,],
			'sql'       => "varchar(32) NOT NULL default ''",
		],
		'pwaStartUrl'                 => [
			'label'      => &$GLOBALS['TL_LANG'][$table]['pwaStartUrl'],
			'inputType'  => 'text',
			'eval'       => ['tl_class'  => 'w50 clr'],
			'sql'        => "varchar(255) NOT NULL default ''",
		],
		'pwaScope'                    => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaScope'],
			'inputType' => 'text',
			'eval'      => [
				'maxlength' => 256,
				'tl_class'  => 'w50',
			],
			'sql'       => "varchar(256) NOT NULL default ''",
		],
		'pwaPreferRelatedApplication' => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaPreferRelatedApplication'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => ['tl_class' => 'w50'],
			'sql'       => "char(1) NOT NULL default ''"
		],
		'pwaRelatedApplications'      => [
			'label'     => &$GLOBALS['TL_LANG'][$table]['pwaRelatedApplications'],
			'exclude'   => true,
			'inputType' => 'multiColumnWizard',
			'eval'      => [
				'tl_class' => 'clr',
				'columnFields' => [
					'plattform' => [
						'label'     => &$GLOBALS['TL_LANG'][$table]['pwaRelatedApplications_plattform'],
						'exclude'   => true,
						'inputType' => 'text',
						'eval'      => ['style' => 'width:180px']
					],
					'url'       => [
						'label'     => &$GLOBALS['TL_LANG'][$table]['pwaRelatedApplications_url'],
						'exclude'   => true,
						'inputType' => 'text',
						'eval'      => ['style' => 'width:180px']
					],
					'id'        => [
						'label'     => &$GLOBALS['TL_LANG'][$table]['pwaRelatedApplications_id'],
						'exclude'   => true,
						'inputType' => 'text',
						'eval'      => ['style' => 'width:180px']
					],
				],
			],
			'sql'       => 'blob NULL',
		],
	],
];