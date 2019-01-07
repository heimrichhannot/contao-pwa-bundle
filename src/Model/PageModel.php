<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Model;


use Contao\Model\Collection;
use HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer;

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
    public static function findAllWithActivePwaConfiguration(array $options = [])
    {
        return static::findByAddPwa(PageContainer::ADD_PWA_YES, $options);
    }
}