<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\Generator;

use Contao\PageModel;
use Contao\StringUtil;
use HeimrichHannot\PwaBundle\Asset\IconBuilderFactory;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Manifest\Manifest;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\Filesystem\Filesystem;

readonly class ManifestGenerator
{
    public function __construct(
        private string                $webDir,
        private IconBuilderFactory $iconBuilderFactory,

    ) {}

    public function getDefaultManifestPath(): string
    {
        return $this->webDir . '/pwa';
    }

    public function generateManifest(Manifest $manifest, string $filename, string $path): void
    {
        $manifestJson = \json_encode($manifest->jsonSerialize(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($path . '/' . $filename, $manifestJson);
    }

    /**
     * Generate the manifest of a page.
     * @throws \Throwable
     */
    public function generatePageManifest(PageModel $page): ?Manifest
    {
        if ($page->addPwa !== PageContainer::ADD_PWA_YES || !$page->pwaConfiguration) {
            return null;
        }

        if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration)) {
            return null;
        }

        $icons = $this->iconBuilderFactory->createBuilderForManifestFromConfig($config)?->buildForManifest();

        $manifest = new Manifest();
        $manifest->name = match ($config->pwaName)
        {
            PwaConfigurationsModel::PWA_NAME_CUSTOM => $config->pwaCustomName,
            PwaConfigurationsModel::PWA_NAME_META_PAGETITLE => $page->pageTitle,
            default => $page->title,
        };

        $manifest->short_name = $config->pwaShortName;
        $manifest->description = $config->pwaDescription;
        $manifest->theme_color = $config->pwaThemeColor ? '#' . $config->pwaThemeColor : '';
        $manifest->background_color = $config->pwaBackgroundColor ? '#' . $config->pwaBackgroundColor : '';
        $manifest->display = $config->pwaDisplay;
        $manifest->lang = $page->language;
        $manifest->dir = $config->pwaDirection;
        $manifest->orientation = $config->pwaOrientation;
        $manifest->start_url = $config->pwaStartUrl;
        $manifest->scope = $config->pwaScope;
        $manifest->prefer_related_applications = (bool)$config->pwaPreferRelatedApplication;
        $manifest->icons = $icons;

        $applications = StringUtil::deserialize($config->pwaRelatedApplications);
        foreach ($applications as $application) {
            $manifest->addRelatedApplication($application['plattform'], $application['url'], $application['id']);
        }

        $filename = $page->alias . '_manifest.json';
        $this->generateManifest($manifest, $filename, $this->getDefaultManifestPath());

        return $manifest;
    }
}