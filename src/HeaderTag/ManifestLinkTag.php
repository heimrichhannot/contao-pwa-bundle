<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\HeaderTag;

use HeimrichHannot\HeadBundle\Head\AbstractLinkTag;

/**
 * @deprecated Head Bundle AbstractLinkTag is deprecated.
 */
class ManifestLinkTag extends AbstractLinkTag
{
    static $name = 'manifest';
}