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

$GLOBALS['TL_DCA']['tl_nc_gateway']['palettes'][\HeimrichHannot\PwaBundle\NotificationCenter\PushGateway::NAME]
    = '{title_legend},title,type;{config_legend},pwaConfiguration';

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pwaConfiguration'] = [
    'inputType' => 'select',
    'eval' => [
        'mandatory' => true,
        'includeBlankOption' => true,
        'tl_class' => 'w50',
    ],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
];
