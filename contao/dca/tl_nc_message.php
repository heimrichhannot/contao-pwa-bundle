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

$GLOBALS['TL_DCA']['tl_nc_message']['palettes'][\HeimrichHannot\PwaBundle\NotificationCenter\PushGateway::NAME]
    = '{title_legend},title,gateway;{languages_legend},languages;{publish_legend},published,start,stop';
