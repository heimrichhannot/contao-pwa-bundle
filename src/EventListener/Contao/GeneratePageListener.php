<?php

namespace HeimrichHannot\PwaBundle\EventListener\Contao;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\FilesModel;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Generator\ConfigurationFileGenerator;
use HeimrichHannot\PwaBundle\Generator\ManifestIconGenerator;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;

#[AsHook('generatePage', priority: 5)]
readonly class GeneratePageListener
{
    public function __construct(
        private ConfigurationFileGenerator $configurationGenerator,
        private ManifestIconGenerator $manifestIconGenerator,
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

        if (!$jsonConfig = \json_encode($this->configurationGenerator->generateConfiguration($rootPage, $config), \JSON_UNESCAPED_UNICODE)) {
            return;
        }

        $manifestUrl = \htmlspecialchars('/pwa/' . $rootPage->alias . '_manifest.json', \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $themeColor = \htmlspecialchars('#' . $config->pwaThemeColor, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $appleMobileWebAppTitle = $config->pwaShortName ?: match ($config->pwaName) {
            PwaConfigurationsModel::PWA_NAME_CUSTOM => $config->pwaCustomName,
            PwaConfigurationsModel::PWA_NAME_META_PAGETITLE => $rootPage->pageTitle,
            default => $rootPage->title,
        };
        $appleMobileWebAppTitle = \htmlspecialchars((string) $appleMobileWebAppTitle, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');

        $appleHead = [];
        if ($iconModel = FilesModel::findByUuid($config->pwaIcons)) {
            $icon = $this->manifestIconGenerator->createIconInstance($iconModel->path, $rootPage->alias);
            $iconUrl = '/' . $icon->getIconsPath() . '/' . $icon->generateIconName('180x180');
            $iconUrl = \htmlspecialchars($iconUrl, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
            $appleHead[] = '<link rel="apple-touch-icon" href="' . $iconUrl . '">';
        }
        if (\in_array($config->pwaDisplay, ['standalone', 'fullscreen'], true)) {
            $appleHead[] = '<meta name="apple-mobile-web-app-capable" content="yes">';
        }
        $appleHead[] = '<meta name="apple-mobile-web-app-status-bar-style" content="default">';
        $appleHead[] = '<meta name="apple-mobile-web-app-title" content="' . $appleMobileWebAppTitle . '">';
        $appleHead = \implode("\n", $appleHead);

        $script = <<<HTML
        <link rel="manifest" href="$manifestUrl">
        <meta name="theme-color" content="$themeColor">
        $appleHead
        <script type="application/json" id="huh-pwa-config">$jsonConfig</script>
        HTML;

        $GLOBALS['TL_HEAD']['huh_pwa'] = $script;
    }
}
