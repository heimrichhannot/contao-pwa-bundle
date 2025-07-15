<?php

namespace HeimrichHannot\PwaBundle\EventListener\Contao;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use HeimrichHannot\UtilsBundle\Util\Utils;

#[AsHook('initializeSystem')]
readonly class AttachAssetsOnInitListener
{
    public function __construct(
        private Utils $utils,
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
        $GLOBALS['TL_JAVASCRIPT']['huh.pwa.bundle'] = 'bundles/heimrichhannotpwa/frontend/contao-pwa-bundle.js';
    }
}