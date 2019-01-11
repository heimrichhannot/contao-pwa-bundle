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

class PwaPushSubscriberContainer
{
    /**
     * @var StringUtil
     */
    private $stringUtil;

    /**
     * PwaPushSubscriberContainer constructor.
     */
    public function __construct(StringUtil $stringUtil)
    {
        $this->stringUtil = $stringUtil;
    }


    /**
     * @param array $row
     * @param string $label
     * @return string
     */
    public function onLabelCallback($row, $label)
    {
        return $this->stringUtil->truncateHtml($label, 80, "…", true, false);
    }
}