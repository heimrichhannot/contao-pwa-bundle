<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

use HeimrichHannot\PwaBundle\DataContainer\PwaPushNotificationContainer;
use HeimrichHannot\UtilsBundle\Dca\DateAddedField;

$table = 'tl_pwa_pushnotifications';

DateAddedField::register($table);

$GLOBALS['TL_DCA'][$table] = [
    'config' => [
        'dataContainer' => \Contao\DC_Table::class,
        'ptable' => 'tl_pwa_configurations',
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['sent ASC, dateSent DESC'],
            'panelLayout' => 'filter;sort,search,limit',
            'headerFields' => ['title'],
        ],
        'label' => [
            'fields' => ['title', 'sent'],
            'format' => '%s %s',
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ],
            'copy' => [
                'label' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? 'Delete this item?')
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'label' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
            ],
        ],
    ],
    'palettes' => [
        '__selector__' => ['clickEvent'],
        'default' => '{message_legend},title,body,icon,iconSize;{behavior_legend},clickEvent;{publish_legend},published,start;',
    ],
    'subpalettes' => [
        'clickEvent_' . PwaPushNotificationContainer::CLICKEVENT_OPEN_PAGE => 'clickJumpTo',
        'clickEvent_' . PwaPushNotificationContainer::CLICKEVENT_OPEN_URL => 'clickUrl',
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid' => [
            'foreignKey' => 'tl_pwa_configurations.title',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'belongsTo', 'load' => 'eager'],
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'dateAdded' => [
            'sorting' => true,
            'flag' => 6,
            'eval' => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => true,],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'body' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'clr'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'icon' => [
            'inputType' => 'fileTree',
            'eval' => [
                'files' => true,
                'filesOnly' => true,
                'extensions' => 'jpg,png,gif',
                'fieldType' => 'radio',
                'tl_class' => 'clr',
            ],
            'sql' => "blob NULL",
        ],
        'iconSize' => [
            'exclude' => true,
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => [
                'rgxp' => 'natural',
                'includeBlankOption' => true,
                'nospace' => true,
                'helpwizard' => true,
                'tl_class' => 'w50',
            ],
            'options_callback' => function () {
                return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(
                    BackendUser::getInstance()
                );
            },
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'receiverCount' => [
            'inputType' => 'text',
            'eval' => [],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'clickEvent' => [
            'inputType' => 'select',
            'options' => [
                PwaPushNotificationContainer::CLICKEVENT_OPEN_PAGE,
                PwaPushNotificationContainer::CLICKEVENT_OPEN_URL,
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_pwa_pushnotifications']['clickEvent'],
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'clickJumpTo' => [
            'exclude' => true,
            'inputType' => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval' => ['fieldType' => 'radio'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'hasOne', 'load' => 'eager'],
        ],
        'clickUrl' => [
            'inputType' => 'text',
            'eval' => ['dcaPicker' => ['providers' => ['newsPicker']], 'tl_class' => 'w50', 'maxlength' => 128],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'sent' => [
            'inputType' => 'checkbox',
            'filter' => true,
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'published' => [
            'filter' => true,
            'flag' => 1,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'start' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard', 'doNotCopy' => true],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'dateSent' => [
            'eval' => ['doNotCopy' => true],
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'flag' => 8,
        ],
    ],
];