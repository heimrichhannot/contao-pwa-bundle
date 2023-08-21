<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use function Symfony\Component\String\u;

class PwaPushSubscriberContainer
{
    /**
     * @Callback(table="tl_pwa_pushsubscriber", target="list.label.label")
     */
    public function onListLabelCallback(array $row, string $label)
    {
        return u($label)->truncate(80, "…")->toString();
    }
}