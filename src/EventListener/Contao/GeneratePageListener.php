<?php

namespace HeimrichHannot\PwaBundle\EventListener\Contao;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Generator\ConfigurationFileGenerator;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;

#[AsHook('generatePage', priority: 5)]
readonly class GeneratePageListener
{
    public function __construct(
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

        if (!$jsonConfig = \json_encode($this->configurationGenerator->generateConfiguration($rootPage, $config), \JSON_UNESCAPED_UNICODE)) {
            return;
        }

        $script = <<<HTML
        <link rel="manifest" href="/pwa/{$rootPage->alias}_manifest.json">
        <meta name="theme-color" content="#{$config->pwaThemeColor}">
        <script type="application/json" id="huh-pwa-config">$jsonConfig</script>
        HTML;

        $GLOBALS['TL_HEAD']['huh_pwa'] = $script;
    }
}
