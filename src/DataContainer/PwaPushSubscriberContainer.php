<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\PwaBundle\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use function Symfony\Component\String\u;

class PwaPushSubscriberContainer
{
    #[AsCallback(table: 'tl_pwa_pushsubscriber', target: 'list.label.label')]
    public function onListLabelCallback(array $row, string $label)
    {
        return u($label)->truncate(80, "…")->toString();
    }
}