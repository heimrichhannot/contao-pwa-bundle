<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\Model;

use Contao\Model\Collection;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;

/**
 * Class PageModel
 *
 * @method static Collection|PageModel[]|PageModel|null findByAddPwa(string $value, array $options)
 *
 * @property string $addPwa
 * @property int $pwaConfiguration
 * @property int $pwaParent
 */
class PageModel extends \Contao\PageModel
{
    /**
     * @param array $options
     * @return Collection|PageModel|PageModel[]|null
     */
    public static function findAllWithActivePwaConfiguration(array $options = []): array|Collection|PageModel|null
    {
        return static::findByAddPwa(PageContainer::ADD_PWA_YES, $options);
    }
}