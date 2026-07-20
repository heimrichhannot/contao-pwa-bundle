<?php

namespace HeimrichHannot\PwaBundle\Asset;

use Contao\PageModel;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Generator\ManifestGenerator;
use HeimrichHannot\PwaBundle\Generator\ServiceWorkerGenerator;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;

readonly class AssetBuilder
{
    public function __construct(
        private ManifestGenerator      $manifestGenerator,
        private ServiceWorkerGenerator $serviceWorkerGenerator
    ) {}

    public function buildForConfig(PwaConfigurationsModel $model): void
    {
        $pages = PageModel::findBy(['addPwa=?', 'pwaConfiguration=?'], [PageContainer::ADD_PWA_YES, $model->id]);
        if (null === $pages) {
            return;
        }

        foreach ($pages as $page) {
            $this->buildForPage($page);
        }
    }

    public function buildForPage(PageModel $page): void
    {
        try {
            $this->manifestGenerator->generatePageManifest($page);
        } catch (\Throwable $e) {
            throw new \RuntimeException(sprintf('Failed to generate manifest for page ID %d: %s', $page->id, $e->getMessage()), 0, $e);
        }
        $result = $this->serviceWorkerGenerator->generatePageServiceworker($page);
        if (false === $result) {
            throw new \RuntimeException(sprintf('Failed to generate service worker for page ID %d', $page->id));
        }
    }
}