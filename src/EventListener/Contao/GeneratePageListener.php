<?php /** @noinspection PhpUndefinedNamespaceInspection, PhpUndefinedClassInspection */

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
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

#[AsHook('generatePage', priority: 5)]
readonly class GeneratePageListener implements ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface         $container,
        private ManifestLinkTag            $manifestLinkTag,
        private ThemeColorMetaTag          $colorMetaTag,
        private PwaHeadScriptTags          $pwaHeadScriptTags,
        private ConfigurationFileGenerator $configurationGenerator
    ) {}

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $rootPage = PageModel::findByPk($pageModel->rootId);

        if (!$rootPage || $rootPage->type !== 'root') {
            return;
        }

        if ($rootPage->addPwa !== PageContainer::ADD_PWA_YES || !$rootPage->pwaConfiguration) {
            return;
        }

        if (!$config = PwaConfigurationsModel::findByPk($rootPage->pwaConfiguration)) {
            return;
        }

        // $this->manifestLinkTag->setContent('/pwa/' . $rootPage->alias . '_manifest.json');
        $this->colorMetaTag->setContent('#' . \ltrim($config->pwaThemeColor, '#'));

        if (!$jsonConfig = \json_encode($this->configurationGenerator->generateConfiguration($rootPage, $config), \JSON_UNESCAPED_UNICODE)) {
            return;
        }

        $script = <<<HTML
        <link rel="manifest" href="/pwa/{$rootPage->alias}_manifest.json">
        <meta name="theme-color" content="#{$config->pwaThemeColor}">
        <script type="application/json" id="huh-pwa-config">$jsonConfig</script>
        <script>window.HuhPWA = JSON.parse('$jsonConfig')</script>
        HTML;

        $GLOBALS['TL_HEAD']['something'] = $script;

        if ($this->container->has(FrontendAsset::class)) {
            $this->container->get(FrontendAsset::class)->addActiveEntrypoint('contao-pwa-bundle');
        }
    }

    public static function getSubscribedServices(): array
    {
        $services = [];

        if (\class_exists(FrontendAsset::class)) {
            $services[] = '?' . FrontendAsset::class;
        }

        return $services;
    }
}