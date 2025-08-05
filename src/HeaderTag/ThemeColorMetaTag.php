<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\HeaderTag;

use HeimrichHannot\HeadBundle\Head\AbstractMetaTag;

/**
 * @deprecated Head Bundle AbstractMetaTag is deprecated.
 */
class ThemeColorMetaTag extends AbstractMetaTag
{
    protected static $name = 'theme-color';
}