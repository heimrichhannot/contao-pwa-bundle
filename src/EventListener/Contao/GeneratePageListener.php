<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\EventListener\Contao;


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
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Hook("generatePage")
 */
class GeneratePageListener implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var ManifestLinkTag
     */
    protected $manifestLinkTag;
    /**
     * @var ThemeColorMetaTag
     */
    protected $colorMetaTag;
    /**
     * @var PwaHeadScriptTags
     */
    protected $pwaHeadScriptTags;
    /**
     * @var ConfigurationFileGenerator
     */
    protected $configurationGenerator;
    /**
     * @var ContainerUtil
     */
    protected $containerUtil;

    /**
     * GeneratePageListener constructor.
     */
    public function __construct(
        ContainerInterface $container,
        ManifestLinkTag $manifestLinkTag,
        ThemeColorMetaTag $colorMetaTag,
        PwaHeadScriptTags $pwaHeadScriptTags,
        ConfigurationFileGenerator $configurationGenerator,
        ContainerUtil $containerUtil
    ) {
        $this->container = $container;
        $this->manifestLinkTag = $manifestLinkTag;
        $this->colorMetaTag = $colorMetaTag;
        $this->pwaHeadScriptTags = $pwaHeadScriptTags;
        $this->configurationGenerator = $configurationGenerator;
        $this->containerUtil = $containerUtil;
    }

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        if (
            $this->containerUtil->isBackend() ||
            ($this->container->has('huh.amp.manager.amp_manager') && true === $this->container->get('huh.amp.manager.amp_manager')->isAmpActive()))
        {
            return;
        }

        $rootPage = PageModel::findByPk($pageModel->rootId);

        if ($rootPage->addPwa === PageContainer::ADD_PWA_YES && $rootPage->pwaConfiguration) {

            $config = PwaConfigurationsModel::findByPk($rootPage->pwaConfiguration);
            if (!$config) {
                return;
            }

            if ($this->container->has('HeimrichHannot\EncoreBundle\Asset\FrontendAsset')) {
                $this->container->get(FrontendAsset::class)->addActiveEntrypoint('contao-pwa-bundle');
            }

            $this->manifestLinkTag->setContent('/pwa/' . $rootPage->alias . '_manifest.json');
            $this->colorMetaTag->setContent('#' . $config->pwaThemeColor);

            $this->pwaHeadScriptTags->addScript("HuhContaoPwaBundle=" . json_encode(
                    $this->configurationGenerator->generateConfiguration($rootPage, $config),
                    JSON_UNESCAPED_UNICODE
                ));
        }
    }

    public static function getSubscribedServices()
    {
        return [
            '?huh.amp.manager.amp_manager',
            '?HeimrichHannot\EncoreBundle\Asset\FrontendAsset',
        ];
    }
}