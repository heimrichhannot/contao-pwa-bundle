<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\EventListener;

use HeimrichHannot\UtilsBundle\Arrays\ArrayUtil;

/**
 * @Hook("initializeSystem")
 */
class InitializeSystemListener
{
    public function __invoke(): void
    {
        ArrayUtil::insertBeforeKey(
            $GLOBALS['TL_HOOKS']['generatePage'],
            'huh.head-bundle',
            'huh.pwa',
            ['huh.pwa.listener.hook', 'onGeneratePage']
        );
    }
}