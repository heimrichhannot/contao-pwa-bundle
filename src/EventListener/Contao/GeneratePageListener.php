<?php

namespace HeimrichHannot\PwaBundle\EventListener\Contao;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\FilesModel;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\PwaBundle\Asset\IconBuilderFactory;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Generator\ConfigurationFileGenerator;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\UtilsBundle\Util\Utils;

#[AsHook('generatePage', priority: 5)]
readonly class GeneratePageListener
{
    public function __construct(
        private ConfigurationFileGenerator $configurationGenerator,
        private IconBuilderFactory $iconBuilderFactory,
        private Utils $utils,
    ) {
    }

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $rootPage = PageModel::findByPk($pageModel->rootId);

        if (!$rootPage || 'root' !== $rootPage->type) {
            return;
        }

        if (PageContainer::ADD_PWA_YES !== $rootPage->addPwa || !$rootPage->pwaConfiguration) {
            return;
        }

        if (!$config = PwaConfigurationsModel::findByPk($rootPage->pwaConfiguration)) {
            return;
        }

        if (!$jsonConfig = \json_encode($this->configurationGenerator->generateConfiguration($rootPage, $config), \JSON_UNESCAPED_UNICODE)) {
            return;
        }

        $manifestUrl = \htmlspecialchars('/pwa/'.$rootPage->alias.'_manifest.json', \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $themeColor = \htmlspecialchars('#'.$config->pwaThemeColor, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');

        $appleHead = $this->appleHead($config, $rootPage);

        $script = <<<HTML
        <link rel="manifest" href="$manifestUrl">
        <meta name="theme-color" content="$themeColor">
        $appleHead
        <script type="application/json" id="huh-pwa-config">$jsonConfig</script>
        HTML;

        $GLOBALS['TL_HEAD']['huh_pwa'] = $script;
    }

    private function appleHead(PwaConfigurationsModel $config, PageModel $rootPage): string
    {
        $appleMobileWebAppTitle = $config->pwaShortName ?: match ($config->pwaName) {
            PwaConfigurationsModel::PWA_NAME_CUSTOM => $config->pwaCustomName,
            PwaConfigurationsModel::PWA_NAME_META_PAGETITLE => $rootPage->pageTitle,
            default => $rootPage->title,
        };

        $appleMobileWebAppTitle = \htmlspecialchars((string) $appleMobileWebAppTitle, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');

        $appleHead = [];
        if ($iconModel = FilesModel::findByUuid($config->pwaIcons)) {
            try {
                $iconUrl = $this->iconBuilderFactory->createIconBuilder()
                    ->setEmptyTargetDirOnBuild(false)
                    ->setTargetDir('assets/images/huh_pwa/app_icons', true)
                    ->setSizes([[180, 180]])
                    ->setFile($iconModel)
                    ->buildPathForFirstSize()
                ;
                $iconUrl = \htmlspecialchars($iconUrl, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
                $appleHead[] = '<link rel="apple-touch-icon" href="'.$iconUrl.'">';
            } catch (\Throwable) {
                $this->utils->container()->log(
                    sprintf('Failed to generate apple touch icon for PWA configuration ID %d', $config->id),
                    __METHOD__,
                    ContaoContext::ERROR
                );
            }
        }
        if (\in_array($config->pwaDisplay, ['standalone', 'fullscreen'], true)) {
            $appleHead[] = '<meta name="apple-mobile-web-app-capable" content="yes">';
        }
        $appleHead[] = '<meta name="apple-mobile-web-app-status-bar-style" content="default">';
        $appleHead[] = '<meta name="apple-mobile-web-app-title" content="'.$appleMobileWebAppTitle.'">';

        return \implode("\n", $appleHead);
    }
}
