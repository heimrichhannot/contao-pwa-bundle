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

use Contao\Message;
use Contao\PageModel;
use HeimrichHannot\PwaBundle\Generator\ManifestGenerator;
use HeimrichHannot\PwaBundle\Generator\ServiceWorkerGenerator;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;

class PageContainer
{
    public const ADD_PWA_NO = 'no';
    public const ADD_PWA_YES = 'yes';
    public const ADD_PWA_INHERIT = 'inherit';

    public function __construct(
        private readonly ManifestGenerator      $manifestGenerator,
        private readonly ServiceWorkerGenerator $serviceWorkerGenerator
    ) {}

    public function onCreateVersionCallback($table, $pid, $version, $row): void
    {
        if ($row['type'] !== 'root' || $row['addPwa'] !== self::ADD_PWA_YES)
        {
            return;
        }

        if (!$page = PageModel::findByPk($row['id']))
        {
            return;
        }

        if (!PwaConfigurationsModel::findByPk($page->pwaConfiguration))
        {
            return;
        }

        try
        {
            $this->manifestGenerator->generatePageManifest($page);
        }
        catch (\Exception $e)
        {
            Message::addError(
                str_replace('%error%', $e->getMessage(), $GLOBALS['TL_LANG']['ERR']['huhPwaGenerateManifest'])
            );
        }

        $this->serviceWorkerGenerator->generatePageServiceworker($page);
    }

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

    public function getInheritPwaPageConfigOptions(): array
    {
        if (!$pages = PageModel::findBy('addPwa', PageContainer::ADD_PWA_YES)) {
            return [];
        }

        $options = [];

        /** @var PageModel $page */
        foreach ($pages as $page)
        {
            if (!$pwaConfig = PwaConfigurationsModel::findByPk($pages->pwaConfiguration)) {
                continue;
            }

            $options[$page->id] = \sprintf('%s (%s)', $page->title, $pwaConfig->title);
        }

        return $options;
    }
}