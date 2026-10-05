<?php

namespace HeimrichHannot\PwaBundle\EventListener\Contao;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use HeimrichHannot\PwaBundle\Asset\FrontendAssetUrls;
use HeimrichHannot\UtilsBundle\Util\Utils;

#[AsHook('initializeSystem')]
readonly class AttachAssetsOnInitListener
{
    public function __construct(
        private Utils $utils,
        private FrontendAssetUrls $frontendAssetUrls,
    ) {}

    public function __invoke(): void
    {
        $this->utils->container()->isBackend()
            ? $this->attachBackendAssets()
            : $this->attachFrontendAssets();
    }

    public function attachBackendAssets(): void
    {
        $GLOBALS['TL_JAVASCRIPT']['huh.pwa.backend'] = 'bundles/heimrichhannotpwa/backend/pwa-backend.js';
        $GLOBALS['TL_CSS']['huh.pwa.backend'] = 'bundles/heimrichhannotpwa/backend/pwa-backend.css';
    }

    public function attachFrontendAssets(): void
    {
        $importMap = json_encode([
            'imports' => $this->frontendAssetUrls->getImportMap(),
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        $GLOBALS['TL_HEAD']['huh_pwa_imports'] = <<<HTML
        
        <script type="importmap" data-turbo-track="reload">
        {$importMap}
        </script>
        <script type="module">import '@hundh/pwa/bundle';</script>

        HTML;
    }
}
