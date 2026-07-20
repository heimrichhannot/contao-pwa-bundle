<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Message;
use Contao\PageModel;
use HeimrichHannot\PwaBundle\Generator\ManifestGenerator;
use HeimrichHannot\PwaBundle\Generator\ServiceWorkerGenerator;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;

class PageContainer
{
    public const TABLE = 'tl_page';

    public const ADD_PWA_NO = 'no';
    public const ADD_PWA_YES = 'yes';
    public const ADD_PWA_INHERIT = 'inherit';

    #[AsCallback(self::TABLE, 'fields.pwaConfiguration.options')]
    public function getPwaConfigurationsAsOptions(): array
    {
        if (!$configs = PwaConfigurationsModel::findAll()) {
            return [];
        }

        $list = [];

        foreach ($configs as $config)
        {
            $list[$config->id] = $config->title;
        }

        return $list;
    }
}