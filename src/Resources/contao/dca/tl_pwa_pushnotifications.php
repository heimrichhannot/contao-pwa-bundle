<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

use HeimrichHannot\ContaoPwaBundle\DataContainer\PwaPushNotificationContainer;

$table = 'tl_pwa_pushnotifications';

$GLOBALS['TL_DCA'][$table] = [
	'config'      => [
		'dataContainer'    => 'Table',
		'ptable'           => 'tl_pwa_configurations',
		'enableVersioning' => true,
		'sql'              => [
			'keys' => [
				'id' => 'primary',
			],
		],
        'onsubmit_callback' => [
            ['huh.utils.dca', 'setDateAdded'],
        ],
	],
	'list'        => [
		'sorting'           => [
			'mode'        => 4,
			'fields'      => ['sent ASC, dateSent DESC'],
			'panelLayout' => 'filter;sort,search,limit',
			'headerFields'          => ['title'],
			'child_record_callback' => ['huh.pwa.datacontainer.pwapushnotification', 'onChildRecordCallback'],
		],
        'label'             => [
            'fields'         => ['title', 'sent'],
            'format'         => '%s %s',
            'group_callback' => ['huh.pwa.datacontainer.pwapushnotification', 'onGroupCallback'],
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
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			],
			'copy'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif',
			],
			'delete' => [
				'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '')
					. '\'))return false;Backend.getScrollOffset()"',
			],
			'show'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			],
		],
	],
	'palettes'    => [
		'__selector__' => ['clickEvent'],
		'default'      => '{message_legend},title,body,icon,iconSize;{behavior_legend},clickEvent;{publish_legend},published,start;',
	],
	'subpalettes' => [
		'clickEvent_' . PwaPushNotificationContainer::CLICKEVENT_OPEN_PAGE => 'clickJumpTo',
		'clickEvent_' . PwaPushNotificationContainer::CLICKEVENT_OPEN_URL => 'clickUrl',
	],
	'fields'      => [
		'id'            => [
			'sql' => "int(10) unsigned NOT NULL auto_increment",
		],
		'pid'           => [
			'foreignKey' => 'tl_pwa_configurations.title',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => ['type' => 'belongsTo', 'load' => 'eager'],
		],
		'tstamp'        => [
			'label' => &$GLOBALS['TL_LANG']['tl_cleaner']['tstamp'],
			'sql'   => "int(10) unsigned NOT NULL default '0'",
		],
		'dateAdded'     => [
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'flag'    => 6,
			'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		],
		'title'         => [
			'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['title'],
			'inputType' => 'text',
			'eval'      => ['tl_class' => 'w50', 'mandatory' => true,],
			'sql'       => "varchar(255) NOT NULL default ''",
		],
		'body'          => [
			'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['body'],
			'inputType' => 'text',
			'eval'      => ['tl_class' => 'clr'],
			'sql'       => "varchar(128) NOT NULL default ''",
		],
		'icon'          => [
			'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['icon'],
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
		'iconSize'      => [
			'label'            => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['iconSize'],
			'exclude'          => true,
			'inputType'        => 'imageSize',
			'reference'        => &$GLOBALS['TL_LANG']['MSC'],
			'eval'             => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
			'options_callback' => function () {
				return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
			},
			'sql'              => "varchar(64) NOT NULL default ''"
		],
		'receiverCount' => [
			'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['receiverCount'],
			'inputType' => 'text',
			'eval'      => [],
			'sql'       => "int(10) unsigned NOT NULL default '0'",
		],
		'clickEvent'    => [
			'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['clickEvent'],
			'inputType' => 'select',
			'options'   => [
				PwaPushNotificationContainer::CLICKEVENT_OPEN_PAGE,
				PwaPushNotificationContainer::CLICKEVENT_OPEN_URL,
			],
			'reference' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['clickEvent'],
			'eval'      => ['tl_class' => 'w50', 'includeBlankOption' => true, 'submitOnChange' => true],
			'sql'       => "varchar(255) NOT NULL default ''",
		],
        'clickJumpTo' => [
            'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['clickJumpTo'],
            'exclude'    => true,
            'inputType'  => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval'       => ['fieldType' => 'radio'],
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => ['type' => 'hasOne', 'load' => 'eager']
        ],
        'clickUrl' => [
            'label'      => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['clickUrl'],
            'inputType'  => 'text',
            'eval'       => ['dcaPicker' => ['providers' => ['newsPicker']], 'tl_class' => 'w50', 'maxlength'=>128],
            'sql'        => "varchar(128) NOT NULL default ''",
        ],
        'sent'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['sent'],
            'inputType' => 'checkbox',
            'filter'    => true,
            'eval'      => ['doNotCopy' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'published'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['published'],
            'filter'    => true,
            'flag'      => 1,
            'inputType' => 'checkbox',
            'eval'      => ['doNotCopy' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'start'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['start'],
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard', 'doNotCopy' => true],
            'sql'       => "varchar(10) NOT NULL default ''"
        ],
        'dateSent'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['dateSent'],
            'eval'      => ['doNotCopy' => true],
            'sql'       => "int(10) unsigned NOT NULL default '0'",
            'flag'      => 8,
        ],
    ],
];
