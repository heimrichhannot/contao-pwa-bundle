import PushSubscriptionButtons from "@hundh/pwa/PushSubscriptionButtons";
import PushNotificationSubscription from "@hundh/pwa/PushNotificationSubscription";
import InstallPrompt from "@hundh/pwa/InstallPrompt";

class HuhPwa {
    /**
     * Create a new PWA instance
     * @param {PwaConfig} config - The configuration for the PWA instance.
     */
    constructor(config) {
        this.config = config || {};
        this.debug = !!(this.config.debug || false);
        this.pushSubscription = new PushNotificationSubscription(this);
        this.buttons = new PushSubscriptionButtons(this);
        this.installPrompt = null;
    }

    debugLog(...args) {
        if (this.debug) {
            console.log('[HuhPwa]', ...args);
        }
    }

    _connected() {
        this.debugLog('[HuhPwa] Connected to PWA instance');
        this.pushSubscription._connected();
        this.buttons._connected();

        if (this.config.hideInstallPrompt) {
            this.installPrompt = new InstallPrompt(this);
            this.installPrompt.registerListener();
        }
    }
}

/**
 * @return {Promise<PwaConfig>}
 */
async function fetchPwaConfig() {
    const $pwaConfig = document.querySelector('script#huh-pwa-config');

    if (!$pwaConfig || !$pwaConfig.textContent) {
        throw new Error('PWA configuration script not found or empty');
    }

    return JSON.parse($pwaConfig.textContent);
}

/**
 * @param {HuhPwa} pwa
 */
function initPush(pwa)
{
    pwa.debugLog("[SW Registration] Initialize Push");

    if (!pwa.config.pushNotifications.support) {
        pwa.debugLog("[SW Registration] Push notifications not activated");
        return;
    }

    pwa.pushSubscription.checkPermission();

    if (!('PushManager' in window)) {
        document.dispatchEvent(new Event('huh_pwa_push_not_supported'));
        pwa.debugLog('[SW Registration] Browser don\'t support push. Hide subscription button.');
    }

    navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
        pwa.debugLog("[SW Registration] Got service worker registration");

        serviceWorkerRegistration.pushManager
            .getSubscription()
            .then(function(subscription) {
                if (subscription) {
                    pwa.debugLog('[SW Registration] Already subscribed');
                    pwa.pushSubscription.setIsSubscribed();
                } else {
                    pwa.debugLog('[SW Registration] Not subscribed');
                    pwa.pushSubscription.setIsUnsubscribed('init');
                }
            });
    });
}

function initServiceWorker(pwa) {
    if (!('serviceWorker' in navigator)) {
        pwa.debugLog('[SW Registration] Service Worker not supported');
        document.dispatchEvent(new CustomEvent('huh_pwa_sw_not_supported'));
        return;
    }

    pwa.debugLog("[SW Registration] Register service worker");

    navigator.serviceWorker.register(pwa.config.serviceWorker.path, {
        scope: pwa.config.serviceWorker.scope
    }).then(function(registration) {
        registration.addEventListener('updatefound', function() {
            pwa.debugLog("[SW Registration] New service worker found for scope " + registration.scope);
        });
        initPush(pwa);
    });
}

fetchPwaConfig().then(config => {
    window.HuhPwaConfig = config;

    const pwa = new HuhPwa(config);

    pwa.debugLog('PWA configuration loaded:', config);
    window.HuhPWA = pwa;

    initServiceWorker(pwa);

    pwa._connected();
}).catch(error => {
    console.info('Failed to fetch PWA configuration:', error);
});
