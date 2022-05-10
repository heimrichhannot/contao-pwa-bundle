<?php

namespace HeimrichHannot\ContaoPwaBundle\EventListener\Contao;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer;
use HeimrichHannot\ContaoPwaBundle\Generator\ConfigurationFileGenerator;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\ManifestLinkTag;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\PwaHeadScriptTags;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\ThemeColorMetaTag;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\EncoreBundle\Asset\FrontendAsset;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * @Hook("generatePage", priority=5)
 */
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
        if ($this->utils->container()->isBackend()
            || ($this->container->has('huh.amp.manager.amp_manager') && true === $this->container->get('huh.amp.manager.amp_manager')->isAmpActive())) {
            return;
        }

        $rootPage = PageModel::findByPk($pageModel->rootId);

        if ($rootPage->addPwa === PageContainer::ADD_PWA_YES && $rootPage->pwaConfiguration) {
            $config = PwaConfigurationsModel::findByPk($rootPage->pwaConfiguration);
            if (!$config) {
                return;
            }

            $this->manifestLinkTag->setContent('/pwa/' . $rootPage->alias . '_manifest.json');
            $this->colorMetaTag->setContent('#' . $config->pwaThemeColor);

            $this->pwaHeadScriptTags->addScript("HuhContaoPwaBundle=" . json_encode(
                    $this->configurationGenerator->generateConfiguration($rootPage, $config),
                    JSON_UNESCAPED_UNICODE
                ));
            if ($this->container->has(FrontendAsset::class)) {
                $this->container->get(FrontendAsset::class)->addActiveEntrypoint('contao-pwa-bundle');
            }
        }
    }

    public static function getSubscribedServices()
    {
        $services = [
            '?huh.amp.manager.amp_manager',
        ];
        if (class_exists(FrontendAsset::class)) {
            $services[] = '?'.FrontendAsset::class;
        }
        return $services;
    }
}