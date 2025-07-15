<?php

/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Generator;

use Contao\FilesModel;
use Contao\PageModel;
use Contao\StringUtil;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Manifest\Manifest;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ManifestGenerator
{
    private readonly string $defaultManifestPath;
    /**
     * @var ManifestIconGenerator
     */
    private $iconGenerator;

    /**
     * ManifestGenerator constructor.
     *
     * @param ContainerInterface    $container
     * @param ManifestIconGenerator $iconGenerator
     */
    public function __construct(string $webDir, ManifestIconGenerator $iconGenerator)
    {
        $this->defaultManifestPath = $webDir . '/pwa';
        $this->iconGenerator = $iconGenerator;
    }

    public function generateManifest(Manifest $manifest, string $filename, string $path)
    {
        $manifestJson = $manifest->jsonSerialize();
        if ($manifest->icons && $manifest->icons->isIconFilesMissing())
        {
            $this->iconGenerator->generateIcons($manifest->icons, $manifest->icons->getApplicationAlias());
            $manifestJson = $manifest->jsonSerialize();
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile($path . '/' . $filename, $manifestJson);
    }

    /**
     * Generate an manifest out of an page
     *
     * @param PageModel|array $page
     * @return bool|Manifest Manifest object or false, if failure.
     */
    public function generatePageManifest(PageModel $page)
    {
        if (($page->addPwa !== PageContainer::ADD_PWA_YES) || !$page->pwaConfiguration)
        {
            return false;
        }
        if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration))
        {
            return false;
        }

        $manifest = new Manifest();
        switch ($config->pwaName)
        {
            case PwaConfigurationsModel::PWA_NAME_CUSTOM:
                $manifest->name = $config->pwaCustomName;
                break;
            case PwaConfigurationsModel::PWA_NAME_META_PAGETITLE:
                $manifest->name = $page->pageTitle;
                break;
            default:
                $manifest->name = $page->title;
        }

        $manifest->short_name = $config->pwaShortName;
        $manifest->description = $config->pwaDescription;
        $manifest->theme_color = '#' . $config->pwaThemeColor;
        $manifest->background_color = '#' . $config->pwaBackgroundColor;
        $manifest->display = $config->pwaDisplay;
        $manifest->lang = $page->language;
        $manifest->dir = $config->pwaDirection;
        $manifest->orientation = $config->pwaOrientation;
        $manifest->start_url = $config->pwaStartUrl;
        $manifest->scope = $config->pwaScope;
        $manifest->prefer_related_applications = $config->pwaPreferRelatedApplication ? true : false;

        $iconModel = FilesModel::findByUuid($config->pwaIcons);
        if ($iconModel)
        {
            $manifest->icons = $this->iconGenerator->createIconInstance($iconModel->path, $page->alias, true);
        }
        $applications = StringUtil::deserialize($config->pwaRelatedApplications);
        foreach ($applications as $application)
        {
            $manifest->addRelatedApplication($application['plattform'], $application['url'], $application['id']);
        }

        $filename = $page->alias . '_manifest.json';
        $this->generateManifest($manifest, $filename, $this->defaultManifestPath);
        return $manifest;
    }
}