import PushSubscriptionButtons from "@hundh/pwa/PushSubscriptionButtons";
import PushNotificationSubscription from "@hundh/pwa/PushNotificationSubscription";
import InstallPrompt from "@hundh/pwa/InstallPrompt";

const STATUS_TIMEOUT = 15000;

class HuhPwa {
    #registration = null;
    #refresh = null;
    #revision = 0;

    constructor(config) {
        this.config = config;
        this.debug = !!config.debug;
        this.pushSubscription = new PushNotificationSubscription(this);
        this.buttons = new PushSubscriptionButtons(this);
        this.installPrompt = new InstallPrompt(this);
        this.installPromptConnected = false;
    }

    debugLog(...args) {
        if (this.debug) console.log('[HuhPwa]', ...args);
    }

    _connected() {
        this.pushSubscription._connected();
        this.buttons._connected();
        if (this.config.hideInstallPrompt && !this.installPromptConnected) {
            this.installPrompt.registerListener();
            this.installPromptConnected = true;
        }
    }

    updateConfig(config) {
        if (JSON.stringify(config.serviceWorker) !== JSON.stringify(this.config.serviceWorker)) {
            this.#registration = null;
        }
        this.invalidateStatus();
        this.config = config;
        this.debug = !!config.debug;
        this.pushSubscription.subscribePath = config.pushNotifications.subscribePath;
        this.pushSubscription.unsubscribePath = config.pushNotifications.unsubscribePath;
        this._connected();
    }

    invalidateStatus() {
        ++this.#revision;
        this.#refresh = null;
    }

    checkPushSupport() {
        let event = null;
        if (!this.config.pushNotifications.support) {
            event = 'huh_pwa_push_not_supported';
        } else if (this.installPrompt.isIos() && !this.installPrompt.isStandalone()) {
            event = 'huh_pwa_push_install_required';
        } else if (!('serviceWorker' in navigator)) {
            event = 'huh_pwa_sw_not_supported';
        } else if (!('PushManager' in window) || !('Notification' in window)) {
            event = 'huh_pwa_push_not_supported';
        }
        if (event) {
            document.dispatchEvent(new Event(event));
            return false;
        }
        return this.pushSubscription.checkPermission();
    }

    async withTimeout(promise, message) {
        let timer;
        try {
            return await Promise.race([
                promise,
                new Promise((resolve, reject) => {
                    timer = setTimeout(() => reject(new Error(message)), STATUS_TIMEOUT);
                }),
            ]);
        } finally {
            clearTimeout(timer);
        }
    }

    getRegistration() {
        if (!this.#registration) {
            const path = this.config.serviceWorker.path;
            const options = {};
            if (this.config.serviceWorker.scope) options.scope = this.config.serviceWorker.scope;
            const pending = this.withTimeout(
                // A synchronous browser error must follow the same failure path.
                Promise.resolve().then(() => navigator.serviceWorker.register(path, options))
                    .then((registration) => this.waitForActiveWorker(registration)),
                'Service worker registration timed out',
            );
            this.#registration = pending;
            pending.catch(() => {
                if (this.#registration === pending) this.#registration = null;
            });
        }
        return this.#registration;
    }

    waitForActiveWorker(registration) {
        // serviceWorker.ready can wait forever and can resolve to a different scope.
        if (registration.active?.state === 'activated') return registration;
        return new Promise((resolve, reject) => {
            const workers = new Set();
            const cleanup = () => {
                clearTimeout(timer);
                registration.removeEventListener('updatefound', check);
                workers.forEach((worker) => worker.removeEventListener('statechange', check));
            };
            const check = () => {
                for (const worker of [registration.installing, registration.waiting, registration.active]) {
                    if (worker && !workers.has(worker)) {
                        workers.add(worker);
                        worker.addEventListener('statechange', check);
                    }
                }
                if (registration.active?.state === 'activated') {
                    cleanup();
                    resolve(registration);
                } else if (workers.size && [...workers].every((worker) => worker.state === 'redundant')) {
                    cleanup();
                    reject(new Error('Service worker activation failed'));
                }
            };
            const timer = setTimeout(() => {
                cleanup();
                reject(new Error('Service worker activation timed out'));
            }, STATUS_TIMEOUT);
            registration.addEventListener('updatefound', check);
            check();
        });
    }

    refreshStatus(context = null) {
        if (this.pushSubscription.busy) return Promise.resolve();
        if (this.#refresh) return this.#refresh;
        const revision = ++this.#revision;
        // Offline/install functionality still needs a worker when push is unavailable.
        const registration = 'serviceWorker' in navigator
            ? this.getRegistration()
            : Promise.reject(new Error('Service workers are unavailable'));
        if (!this.checkPushSupport()) {
            registration.catch((error) => this.debugLog('[SW Registration]', error));
            return Promise.resolve();
        }
        document.dispatchEvent(new Event('huh_pwa_push_checking'));
        const refresh = this.withTimeout(
            registration.then((worker) => worker.pushManager.getSubscription()),
            'Push subscription check timed out',
        ).then((subscription) => {
            if (revision !== this.#revision) return;
            subscription ? this.pushSubscription.setIsSubscribed() : this.pushSubscription.setIsUnsubscribed(context);
        }).catch((reason) => {
            if (revision !== this.#revision) return;
            this.#registration = null;
            this.debugLog('[Push initialization]', reason);
            document.dispatchEvent(new CustomEvent('huh_pwa_push_initialization_failed', { detail: { reason } }));
        }).finally(() => {
            if (this.#refresh === refresh) this.#refresh = null;
        });
        this.#refresh = refresh;
        return refresh;
    }
}

let configText = null;
function initialize(context = null) {
    const scripts = document.querySelectorAll('script#huh-pwa-config');
    const text = scripts[scripts.length - 1]?.textContent;
    if (!text) return;
    if (text !== configText) {
        try {
            const config = JSON.parse(text);
            if (!config.serviceWorker?.path || !config.pushNotifications || !config.translations?.pushnotifications) {
                throw new Error('Incomplete PWA configuration');
            }
            window.HuhPwaConfig = config;
            if (window.HuhPWA) {
                window.HuhPWA.updateConfig(config);
            } else {
                window.HuhPWA = new HuhPwa(config);
                window.HuhPWA._connected();
            }
            configText = text;
            context = 'init';
        } catch (reason) {
            console.info('Failed to load PWA configuration:', reason);
            return;
        }
    }
    window.HuhPWA.refreshStatus(context);
}

initialize('init');
document.addEventListener('DOMContentLoaded', () => initialize('init'), { once: true });
for (const event of ['turbo:load', 'turbo:frame-load']) {
    document.addEventListener(event, () => initialize('navigation'));
}
window.addEventListener('pageshow', () => initialize('resume'));
window.addEventListener('focus', () => initialize('resume'));
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') initialize('resume');
});
new MutationObserver((mutations) => {
    const configChanged = mutations.some(({ target, addedNodes }) =>
        target.id === 'huh-pwa-config' || [...addedNodes].some((node) =>
            node instanceof Element && (node.matches('script#huh-pwa-config') || node.querySelector('script#huh-pwa-config'))
        )
    );
    if (configChanged) initialize('navigation');
}).observe(document.documentElement, { childList: true, subtree: true, characterData: true });
