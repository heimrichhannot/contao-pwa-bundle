/**
 * Run in Chrome on a page containing rendered PWA subscription buttons.
 * Paste this function into DevTools, then await runPwaStatusChecks().
 * Uses the real bundle and DOM; substitutes browser/network failures without
 * creating or removing actual push subscriptions. Restores the original state.
 */
async function runPwaStatusChecks() {
    const pwa = window.HuhPWA;
    if (!pwa || !document.querySelector('.huhPwaWebSubscription')) {
        throw new Error('Open a PWA page containing subscription buttons first');
    }
    const originalConfig = structuredClone(pwa.config);
    const originals = [];
    const results = [];
    const translations = originalConfig.translations.pushnotifications;
    const originalFetch = window.fetch;
    const originalTimeout = window.setTimeout;
    let registration;
    let subscribeCalls = 0;
    let serverStatus = 200;
    let failureEvents = 0;
    let testIndex = 0;
    const subscription = { unsubscribe: async () => true };
    const onFailure = () => ++failureEvents;

    function override(object, key, value) {
        originals.push([object, key, Object.getOwnPropertyDescriptor(object, key)]);
        Object.defineProperty(object, key, { configurable: true, writable: true, value });
    }
    function worker(state) {
        return Object.assign(new EventTarget(), { state });
    }
    function ready(subscribed = true) {
        return Object.assign(new EventTarget(), {
            active: worker('activated'),
            pushManager: {
                getSubscription: async () => subscribed ? subscription : null,
                subscribe: async () => { ++subscribeCalls; return subscription; },
            },
        });
    }
    function buttons() {
        return [...document.querySelectorAll('.huhPwaWebSubscription')];
    }
    function assert(condition, message) {
        if (!condition) throw new Error(message);
    }
    function assertState(label, disabled) {
        assert(buttons().every((button) => button.querySelector('.label').textContent === label && button.disabled === disabled), 'Unexpected button state: ' + label);
    }
    async function check(name, action) {
        await action();
        results.push(name);
    }
    async function refresh(config = originalConfig) {
        const next = structuredClone(config);
        next.serviceWorker.path += '?status-test=' + ++testIndex;
        pwa.updateConfig(next);
        await pwa.refreshStatus('test');
    }
    async function settled() {
        for (let i = 0; i < 100 && pwa.pushSubscription.busy; ++i) {
            await new Promise((resolve) => originalTimeout(resolve, 5));
        }
        assert(!pwa.pushSubscription.busy, 'Operation remained busy');
        await pwa.refreshStatus('test');
    }

    try {
        override(navigator.serviceWorker, 'register', async () => {
            if (registration instanceof Error) throw registration;
            return registration;
        });
        override(window, 'Notification', { permission: 'granted', requestPermission: async () => 'granted' });
        override(navigator, 'userAgent', 'PWA status test desktop');
        override(navigator, 'platform', 'Linux');
        override(navigator, 'maxTouchPoints', 0);
        override(navigator, 'standalone', false);
        override(window, 'setTimeout', (callback, delay, ...args) => originalTimeout(callback, delay === 15000 ? 50 : delay, ...args));
        override(window, 'fetch', async (input, options) => {
            const path = typeof input === 'string' ? input : input.url;
            if (path === '/_huh_pwa/vapid.pub') return new Response('BA');
            if (path.startsWith('/_huh_pwa/notification/')) return new Response('', { status: serverStatus });
            return originalFetch(input, options);
        });
        document.addEventListener('huh_pwa_push_subscription_failed', onFailure);

        await check('subscribed: all buttons enabled', async () => {
            registration = ready();
            await refresh();
            assertState(translations.unsubscribe, false);
        });
        await check('unsubscribed: all buttons enabled', async () => {
            registration = ready(false);
            await refresh();
            assertState(translations.subscribe, false);
        });
        await check('denied permission', async () => {
            Notification.permission = 'denied';
            await refresh();
            assertState(translations.blocked, true);
            Notification.permission = 'granted';
        });
        await check('unsupported Push API', async () => {
            const descriptor = Object.getOwnPropertyDescriptor(window, 'PushManager');
            try {
                delete window.PushManager;
                await refresh();
                assertState(translations.not_supported, true);
            } finally {
                Object.defineProperty(window, 'PushManager', descriptor);
            }
        });
        await check('disabled push configuration', async () => {
            const config = structuredClone(originalConfig);
            config.pushNotifications.support = false;
            await refresh(config);
            assertState(translations.not_supported, true);
        });
        await check('iPhone browser: short label and accessible help', async () => {
            navigator.userAgent = 'iPhone';
            await refresh();
            assertState(translations.install_required, true);
            const ids = buttons().map((button) => button.nextElementSibling.id);
            assert(new Set(ids).size === ids.length, 'Help IDs must be unique');
            assert(buttons().every((button) => !button.nextElementSibling.hidden && button.nextElementSibling.textContent === translations.install_required_help && button.getAttribute('aria-describedby').split(' ').includes(button.nextElementSibling.id)), 'Missing installation help');
        });
        await check('iPhone installed app: normal push state', async () => {
            navigator.standalone = true;
            registration = ready();
            await refresh();
            assertState(translations.unsubscribe, false);
            assert(buttons().every((button) => button.nextElementSibling.hidden), 'Installation help remained visible');
            navigator.standalone = false;
        });
        await check('iPad desktop user agent', async () => {
            navigator.userAgent = 'Macintosh';
            navigator.platform = 'MacIntel';
            navigator.maxTouchPoints = 5;
            await refresh();
            assertState(translations.install_required, true);
        });
        await check('Mac desktop: no installation requirement', async () => {
            navigator.maxTouchPoints = 0;
            await refresh();
            assertState(translations.unsubscribe, false);
        });
        await check('registration failure: retry and explanation', async () => {
            registration = new Error('Controlled registration failure');
            await refresh();
            assertState(translations.retry, false);
            assert(buttons().every((button) => !button.nextElementSibling.hidden && button.nextElementSibling.textContent === translations.initialization_failed), 'Missing failure help');
        });
        await check('retry click recovers all buttons', async () => {
            registration = ready();
            buttons()[0].click();
            await pwa.refreshStatus('test');
            assertState(translations.unsubscribe, false);
        });
        await check('registration timeout', async () => {
            registration = new Promise(() => {});
            await refresh();
            assertState(translations.retry, false);
        });
        await check('worker activation timeout', async () => {
            registration = Object.assign(new EventTarget(), { installing: worker('installing') });
            await refresh();
            assertState(translations.retry, false);
        });
        await check('worker activation completes', async () => {
            registration = ready(false);
            registration.installing = worker('installing');
            registration.active = null;
            originalTimeout(() => {
                registration.active = worker('activated');
                registration.installing.dispatchEvent(new Event('statechange'));
            }, 10);
            await refresh();
            assertState(translations.subscribe, false);
        });
        await check('subscription lookup failure', async () => {
            registration = ready();
            registration.pushManager.getSubscription = async () => { throw new Error('Controlled lookup failure'); };
            await refresh();
            assertState(translations.retry, false);
        });
        await check('subscription lookup timeout', async () => {
            registration = ready();
            registration.pushManager.getSubscription = () => new Promise(() => {});
            await refresh();
            assertState(translations.retry, false);
        });
        await check('stale result cannot overwrite current configuration', async () => {
            let complete;
            registration = ready();
            registration.pushManager.getSubscription = () => new Promise((resolve) => { complete = resolve; });
            const old = refresh();
            await new Promise((resolve) => originalTimeout(resolve, 5));
            registration = ready(false);
            await refresh();
            complete(subscription);
            await old;
            assertState(translations.subscribe, false);
        });
        await check('concurrent subscribe actions run once', async () => {
            registration = ready(false);
            registration.pushManager.subscribe = async () => {
                ++subscribeCalls;
                await new Promise((resolve) => originalTimeout(resolve, 10));
                registration.pushManager.getSubscription = async () => subscription;
                return subscription;
            };
            await refresh();
            document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', { detail: 'subscribe' }));
            assert(buttons().every((button) => button.disabled), 'All buttons must be disabled while subscribing');
            document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', { detail: 'subscribe' }));
            await settled();
            assert(subscribeCalls === 1, 'Duplicate subscription operation');
            assertState(translations.unsubscribe, false);
        });
        await check('HTTP failure emits failure event and restores controls', async () => {
            registration = ready(false);
            serverStatus = 500;
            await refresh();
            document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', { detail: 'subscribe' }));
            await settled();
            assert(failureEvents === 1, 'HTTP failure was ignored');
            assertState(translations.subscribe, false);
            serverStatus = 200;
        });
        await check('silent browser block explains the site settings', async () => {
            registration = ready(false);
            Notification.permission = 'default';
            Notification.requestPermission = async () => 'denied';
            await refresh();
            buttons()[0].click();
            await settled();
            assertState(translations.subscribe, false);
            assert(buttons().every((button) => !button.nextElementSibling.hidden && button.nextElementSibling.textContent === translations.permission_blocked_by_browser), 'Missing browser block help');
        });
        await check('dismissed prompt clears the browser block help', async () => {
            Notification.requestPermission = async () => 'default';
            buttons()[0].click();
            await settled();
            assertState(translations.subscribe, false);
            assert(buttons().every((button) => button.nextElementSibling.hidden), 'Browser block help remained visible');
            Notification.permission = 'granted';
            Notification.requestPermission = async () => 'granted';
        });
        return { passed: results.length, checks: results };
    } finally {
        document.removeEventListener('huh_pwa_push_subscription_failed', onFailure);
        for (const [object, key, descriptor] of originals.reverse()) {
            if (descriptor) Object.defineProperty(object, key, descriptor);
            else delete object[key];
        }
        pwa.updateConfig(originalConfig);
        await pwa.refreshStatus('resume');
    }
}
