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


use HeimrichHannot\UtilsBundle\String\StringUtil;
use function Symfony\Component\String\u;

class PwaPushSubscriberContainer
{
    /**
     * @param array $row
     * @param string $label
     * @return string
     */
    public function onLabelCallback($row, $label)
    {
        return u($label)->truncate(80, "…");
    }
}