<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

use HeimrichHannot\PwaBundle\DataContainer\PwaConfigurationContainer;

$table = 'tl_pwa_configurations';

$GLOBALS['TL_DCA'][$table] = [
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'ctable' => ['tl_pwa_pushsubscriber'],
        'onsubmit_callback' => [
            ['huh.utils.dca', 'setDateAdded'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'label' => [
            'fields' => ['title', 'supportPush'],
            'format' => '%s <span style="color:#999;padding-left:3px">(Push: %s)</span>',
        ],
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'panelLayout' => 'filter;search,limit',
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'control' => [
                'label' => &$GLOBALS['TL_LANG']['tl_pwa_configurations']['control'],
                'href' => 'huh_pwa.backend.control',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
                'class' => 'header_icon',
                'icon' => 'wrench.svg',
            ],
        ],
        'operations' => [
            'pushNotifications' => [
                'href' => 'table=tl_pwa_pushnotifications',
                'icon' => 'edit.svg',
            ],
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'copy' => [
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['tl_pwa_configurations']['deleteConfirm'] ?? 'Delete this item?')
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.gif',
            ],
            'subscriber' => [
                'href' => 'table=tl_pwa_pushsubscriber',
                'icon' => 'mgroup.svg',
            ],
        ],
    ],
    'palettes' => [
        '__selector__' => ['pwaName', 'sendWithCron'],
        'default' => '{general_legend},title,supportPush,sendWithCron,addDebugLog;'
            . '{application_legend},hideInstallPrompt;'
            . '{serviceworker_legend},serviceWorkerTemplate,offlinePage;'
            . '{manifest_legend},pwaName,pwaShortName,pwaDescription,pwaThemeColor,pwaBackgroundColor,pwaIcons,pwaDirection,pwaDisplay,pwaOrientation,pwaStartUrl,pwaScope,pwaRelatedApplications,pwaPreferRelatedApplication',
    ],
    'subpalettes' => [
        'pwaName_custom' => 'pwaCustomName',
        'sendWithCron' => 'cronIntervall',
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
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
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'addDebugLog' => [
            'inputType' => 'checkbox',
            'search' => true,
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'sendWithCron' => [
            'inputType' => 'checkbox',
            'filter' => true,
            'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'cronIntervall' => [
            'inputType' => 'select',
            'options' => ['minutely', 'hourly', 'daily', 'weekly', 'monthly'],
            'eval' => [
                'tl_class' => 'w50',
                'includeBlankOption' => false,
                'default' => 'hourly',
            ],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'hideInstallPrompt' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'serviceWorkerTemplate' => [
            'inputType' => 'select',
            'eval' => [
                'tl_class' => 'w50 clr',
                'includeBlankOption' => false,
            ],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'offlinePage' => [
            'exclude' => true,
            'inputType' => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'hasOne', 'load' => 'eager'],
        ],
        'pwaName' => [
            'inputType' => 'select',
            'options' => [\HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel::PWA_NAME_OPTIONS],
            'reference' => &$GLOBALS['TL_LANG'][$table]['pwaName'],
            'eval' => [
                'tl_class' => 'w50',
                'includeBlankOption' => false,
                'default' => 'title',
                'submitOnChange' => true,
            ],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'pwaCustomName' => [
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 128,
                'tl_class' => 'w50 clr',
            ],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'pwaShortName' => [
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 32,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'pwaBackgroundColor' => [
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 6,
                'colorpicker' => true,
                'isHexColor' => true,
                'decodeEntities' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => "varchar(16) NOT NULL default ''",
        ],
        'pwaThemeColor' => [
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 6,
                'colorpicker' => true,
                'isHexColor' => true,
                'decodeEntities' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'pwaDescription' => [
            'inputType' => 'textarea',
            'eval' => [
                'tl_class' => 'clr',
            ],
            'sql' => "text NULL",
        ],
        'pwaDirection' => [
            'inputType' => 'select',
            'options' => \HeimrichHannot\PwaBundle\Manifest\Manifest::DIR_VALUES,
            'reference' => &$GLOBALS['TL_LANG']['tl_page']['pwaDir'],
            'eval' => [
                'maxlength' => 4,
                'tl_class' => 'w50',
                'includeBlankOption' => true,
            ],
            'sql' => "varchar(4) NOT NULL default ''",
        ],
        'pwaDisplay' => [
            'inputType' => 'select',
            'options' => \HeimrichHannot\PwaBundle\Manifest\Manifest::DISPLAY_VALUES,
            'reference' => &$GLOBALS['TL_LANG']['tl_page']['pwaDisplay'],
            'eval' => [
                'tl_class' => 'w50',
                'includeBlankOption' => true,
                'mandatory' => true,
            ],
            'sql' => "varchar(16) NOT NULL default ''",
        ],
        'pwaIcons' => [
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
        'pwaOrientation' => [
            'inputType' => 'select',
            'options' => \HeimrichHannot\PwaBundle\Manifest\Manifest::ORIENTATION_VALUES,
            'reference' => &$GLOBALS['TL_LANG']['tl_page']['pwaOrientation'],
            'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true,],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'pwaStartUrl' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'pwaScope' => [
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 256,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(256) NOT NULL default ''",
        ],
        'pwaPreferRelatedApplication' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'pwaRelatedApplications' => [
            'exclude' => true,
            'inputType' => 'multiColumnWizard',
            'eval' => [
                'tl_class' => 'clr',
                'columnFields' => [
                    'plattform' => [
                        'label' => &$GLOBALS['TL_LANG'][$table]['pwaRelatedApplications_plattform'],
                        'exclude' => true,
                        'inputType' => 'text',
                        'eval' => ['style' => 'width:180px'],
                    ],
                    'url' => [
                        'label' => &$GLOBALS['TL_LANG'][$table]['pwaRelatedApplications_url'],
                        'exclude' => true,
                        'inputType' => 'text',
                        'eval' => ['style' => 'width:180px'],
                    ],
                    'id' => [
                        'label' => &$GLOBALS['TL_LANG'][$table]['pwaRelatedApplications_id'],
                        'exclude' => true,
                        'inputType' => 'text',
                        'eval' => ['style' => 'width:180px'],
                    ],
                ],
            ],
            'sql' => 'blob NULL',
        ],
        'supportPush' => [
            'inputType' => 'checkbox',
            'filter' => true,
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default ''",
        ],
    ],
];