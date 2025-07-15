<?php

namespace HeimrichHannot\PwaBundle\EventListener\Contao;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Generator\ConfigurationFileGenerator;
use HeimrichHannot\PwaBundle\HeaderTag\ManifestLinkTag;
use HeimrichHannot\PwaBundle\HeaderTag\PwaHeadScriptTags;
use HeimrichHannot\PwaBundle\HeaderTag\ThemeColorMetaTag;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\EncoreBundle\Asset\FrontendAsset;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

#[AsHook('generatePage', priority: 5)]
class GeneratePageListener implements ServiceSubscriberInterface
{
    private ContainerInterface         $container;
    private Utils                      $utils;
    private ManifestLinkTag            $manifestLinkTag;
    private ThemeColorMetaTag          $colorMetaTag;
    private PwaHeadScriptTags          $pwaHeadScriptTags;
    private ConfigurationFileGenerator $configurationGenerator;

    public function __construct(
        ContainerInterface $container,
        Utils $utils,
        ManifestLinkTag $manifestLinkTag,
        ThemeColorMetaTag $colorMetaTag,
        PwaHeadScriptTags $pwaHeadScriptTags,
        ConfigurationFileGenerator $configurationGenerator
    ) {
        $this->container = $container;
        $this->utils = $utils;
        $this->manifestLinkTag = $manifestLinkTag;
        $this->colorMetaTag = $colorMetaTag;
        $this->pwaHeadScriptTags = $pwaHeadScriptTags;
        $this->configurationGenerator = $configurationGenerator;
    }

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $rootPage = PageModel::findByPk($pageModel->rootId);

        if ($rootPage->addPwa === PageContainer::ADD_PWA_YES && $rootPage->pwaConfiguration) {
            $config = PwaConfigurationsModel::findByPk($rootPage->pwaConfiguration);
            if (!$config) {
                return;
            }

            $this->manifestLinkTag->setContent('/pwa/' . $rootPage->alias . '_manifest.json');
            $this->colorMetaTag->setContent('#' . $config->pwaThemeColor);

            $this->pwaHeadScriptTags->addScript("HuhPWA=" . json_encode(
                    $this->configurationGenerator->generateConfiguration($rootPage, $config),
                    JSON_UNESCAPED_UNICODE
                ));
            if ($this->container->has(FrontendAsset::class)) {
                $this->container->get(FrontendAsset::class)->addActiveEntrypoint('contao-pwa-bundle');
            }
        }
    }

    public static function getSubscribedServices(): array
    {
        $services = [];
        if (class_exists(FrontendAsset::class)) {
            $services[] = '?'.FrontendAsset::class;
        }
        return $services;
    }
}