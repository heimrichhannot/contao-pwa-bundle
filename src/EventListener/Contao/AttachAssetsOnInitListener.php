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
        $GLOBALS['TL_HEAD']['huh.pwa.bundle'] = <<<HTML
        
        <script type="importmap">
        {
            "imports": {
                "@hundh/pwa/bundle": "/bundles/heimrichhannotpwa/frontend/huh-pwa.js",
                "@hundh/pwa/serviceworker": "/bundles/heimrichhannotpwa/frontend/huh-pwa-serviceworker.js",
                "@hundh/pwa/InstallPrompt": "/bundles/heimrichhannotpwa/frontend/InstallPrompt.js",
                "@hundh/pwa/PushNotificationSubscription": "/bundles/heimrichhannotpwa/frontend/PushNotificationSubscription.js",
                "@hundh/pwa/PushSubscriptionButtons": "/bundles/heimrichhannotpwa/frontend/PushSubscriptionButtons.js"
            }
        }
        </script>
        <script type="module">import '@hundh/pwa/bundle';</script>

        HTML;
    }
}