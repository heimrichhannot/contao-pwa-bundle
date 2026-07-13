<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

if (!interface_exists(\Terminal42\NotificationCenterBundle\Gateway\GatewayInterface::class)) {
    return;
}

$GLOBALS['TL_DCA']['tl_nc_language']['palettes'][\HeimrichHannot\PwaBundle\NotificationCenter\PushGateway::NAME]
    = '{general_legend},language,fallback;{push_legend},push_title,push_body,push_icon,push_url';

$GLOBALS['TL_DCA']['tl_nc_language']['fields']['push_title'] = [
    'search' => true,
    'inputType' => 'text',
    'eval' => [
        'decodeEntities' => true,
        'mandatory' => true,
        'maxlength' => 255,
        'tl_class' => 'long clr',
    ],
    'nc_context' => \Terminal42\NotificationCenterBundle\Token\TokenContext::Text,
    'sql' => ['type' => 'string', 'length' => 255, 'default' => null, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_nc_language']['fields']['push_body'] = [
    'search' => true,
    'inputType' => 'textarea',
    'eval' => [
        'decodeEntities' => true,
        'tl_class' => 'clr',
    ],
    'nc_context' => \Terminal42\NotificationCenterBundle\Token\TokenContext::Text,
    'sql' => ['type' => 'text', 'default' => null, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_nc_language']['fields']['push_icon'] = [
    'inputType' => 'fileTree',
    'eval' => [
        'fieldType' => 'radio',
        'files' => true,
        'filesOnly' => true,
        'tl_class' => 'clr',
    ],
    'sql' => ['type' => 'blob', 'length' => 65535, 'default' => null, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_nc_language']['fields']['push_url'] = [
    'search' => true,
    'inputType' => 'text',
    'eval' => [
        'decodeEntities' => true,
        'maxlength' => 2048,
        'tl_class' => 'long clr',
    ],
    'nc_context' => \Terminal42\NotificationCenterBundle\Token\TokenContext::Text,
    'sql' => ['type' => 'text', 'default' => null, 'notnull' => false],
];
