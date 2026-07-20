<?php

namespace HeimrichHannot\PwaBundle\Asset;

use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Generator\ManifestGenerator;
use HeimrichHannot\PwaBundle\Generator\ServiceWorkerGenerator;
use HeimrichHannot\PwaBundle\Model\PageModel;
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
            $this->manifestGenerator->generatePageManifest($page);
            $result = $this->serviceWorkerGenerator->generatePageServiceworker($page);
            if (false === $result) {
                throw new \RuntimeException(sprintf('Failed to generate service worker for page ID %d', $page->id));
            }
        }
    }
}